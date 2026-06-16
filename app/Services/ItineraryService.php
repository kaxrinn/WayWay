<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Orchestrator: Menggabungkan semua langkah pipeline
 *
 * Pipeline:
 * 1. ContentFilterService   → filter by kategori, budget, status
 * 2. BayesianScoringService → rank by rating + value + companion fit
 * 3. HaversineService       → hitung jarak, build distance matrix
 * 4. GreedyRouterService    → susun rute optimal
 * 5. OsrmService            → validasi dengan jalan nyata
 */
class ItineraryService
{
    public function __construct(
        private ContentFilterService  $contentFilter,
        private BayesianScoringService $bayesian,
        private HaversineService      $haversine,
        private GreedyRouterService   $greedy,
        private OsrmService           $osrm,
    ) {}

    /**
     * @param array $params {
     *   kategori_ids: int[],
     *   budget: float,
     *   companion: string (keluarga|pasangan|solo|grup),
     *   tanggal: string (Y-m-d),
     *   origin_lat: float,
     *   origin_lon: float,
     *   max_destinations: int,
     *   available_hours: float,
     * }
     */
public function generate(array $params): array
{
    $cacheKey = 'itinerary_' . md5(json_encode($params));

    return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($params) {

        // Definisikan origin sekali di atas, pakai di semua step
        $originLat = (float) ($params['origin_lat'] ?? 1.1296758);
        $originLon = (float) ($params['origin_lon'] ?? 104.0452254);

        // === STEP 1: Content-Based Filtering ===
        $candidates = $this->contentFilter->filter(
            $params['kategori_ids'],
            $params['budget'],
            $params['tanggal'],
            $originLat,
            $originLon,
            10.0  // radius 10 km dari titik keberangkatan
        );

        if ($candidates->isEmpty()) {
            return ['error' => 'No destinations found within 10 km of your location.', 'route' => []];
        }

        // Adjust untuk tipe companion
        $candidates = $this->contentFilter->adjustForCompanion(
            $candidates,
            $params['companion']
        );

        // === STEP 2: Bayesian Scoring ===
        $ranked = $this->bayesian->rankCandidates($candidates);

        // === STEP 3: Haversine ===
        $ranked = $this->haversine->attachDistances($ranked, $originLat, $originLon);

        // === STEP 4: Greedy Route ===
        $maxDest          = (int) ($params['max_destinations'] ?? 6);
        $availableMinutes = max(
            (int) (($params['available_hours'] ?? 8) * 60),
            $maxDest * 120
        );

        $routeData = $this->greedy->buildRoute(
            $ranked,
            $originLat,
            $originLon,
            $maxDest,
            $availableMinutes
        );

        // === STEP 5: OSRM Validation ===
        $routeData = $this->osrm->validateRoute($routeData);

        $routeData['meta'] = [
            'total_candidates' => $candidates->count(),
            'total_ranked'     => $ranked->count(),
            'generated_at'     => now()->toDateTimeString(),
            'params'           => $params,
            'osrm_validated'   => $routeData['osrm_validated'] ?? false,
        ];

        $routeData['schedule'] = $this->buildDaySchedule($routeData['route']);

        return $routeData;
    });
}

    /**
     * Susun jadwal harian dengan estimasi waktu
     */
    private function buildDaySchedule(array $route): array
    {
        $schedule   = [];
        $currentTime = \Carbon\Carbon::today()->setHour(9)->setMinute(0); // mulai jam 9 pagi

        foreach ($route as $stop) {
            // Waktu perjalanan ke sini
            if ($stop['order'] > 1) {
                $currentTime->addMinutes($stop['travel_minutes']);
            }

            $arrivalTime = $currentTime->format('H:i');
            $currentTime->addMinutes($stop['visit_duration']);
            $departureTime = $currentTime->format('H:i');

            $schedule[] = [
                'stop'           => $stop,
                'arrival_time'   => $arrivalTime,
                'departure_time' => $departureTime,
            ];

            // Buffer 10 menit antar lokasi (parkir, jalan kaki, dll)
            $currentTime->addMinutes(10);
        }

        return $schedule;
    }
}
