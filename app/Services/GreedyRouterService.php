<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * Greedy Nearest Neighbor Route Optimizer
 * + Category Diversity: memastikan kategori tersebar merata
 */
class GreedyRouterService
{
private array $visitDurationByCategory = [
    'Beaches'                  => 120,
    'Man-Made Attractions'     => 120,
    'Cultural & Heritage Sites'=> 90,
    'Restaurants'              => 60,
    'Local Eateries'           => 60,
    'Coffee Shops'             => 45,
    'Religious Sites'          => 60,
    'Hotels & Accommodation'   => 60,
    'Shopping Mall'            => 90,
    'Nature & Eco Tourism'     => 150,
    'Transportation Hubs'      => 30,
    'Fitness Centers'          => 90,
    'Salon & SPA'              => 90,
    'Cinemas'                  => 120,
    'Nightlife'                => 120,
    'default'                  => 90,
];
    private HaversineService $haversine;

    public function __construct(HaversineService $haversine)
    {
        $this->haversine = $haversine;
    }

    public function buildRoute(
        Collection $rankedCandidates,
        float $originLat,
        float $originLon,
        int $maxDestinations = 6,
        int $availableMinutes = 480
    ): array {
        $candidates = $rankedCandidates->take(min(50, $rankedCandidates->count()));

        if ($candidates->isEmpty()) {
            return ['route' => [], 'total_distance' => 0, 'total_minutes' => 0];
        }

        $distMatrix = $this->haversine->buildDistanceMatrix($candidates);

        $visited          = [];
        $visitedCategories = []; // track kategori yang sudah masuk
        $route            = [];
        $totalDistance    = 0;
        $totalMinutes     = 0;
        $currentLat       = $originLat;
        $currentLon       = $originLon;
        $currentId        = null;

        // Mulai dari destinasi skor tertinggi
        $first = $candidates->first();
        $visited[]   = $first->id;
        $visitedCategories[] = $first->kategori->nama_kategori ?? 'default';

        $distToFirst   = $this->haversine->distance($originLat, $originLon, (float)$first->latitude, (float)$first->longitude);
        $travelFirst   = $this->haversine->estimateTravelTime($distToFirst);
        $visitFirst    = $this->getVisitDuration($first);

        $route[] = $this->buildStop($first, 1, $distToFirst, $travelFirst);

        $totalDistance += $distToFirst;
        $totalMinutes  += $travelFirst + $visitFirst;
        $currentLat     = (float) $first->latitude;
        $currentLon     = (float) $first->longitude;
        $currentId      = $first->id;

        $stopNumber = 2;

        while (
            count($visited) < $maxDestinations
            && $totalMinutes < $availableMinutes
        ) {
            $bestNext     = null;
            $bestScore    = -1;
            $bestDistance = PHP_FLOAT_MAX;

            foreach ($candidates as $candidate) {
                if (in_array($candidate->id, $visited)) continue;

                $dist = $distMatrix[$currentId][$candidate->id] ?? 9999;

                $travelTime = $this->haversine->estimateTravelTime($dist);
                $visitTime  = $this->getVisitDuration($candidate);

                if ($totalMinutes + $travelTime + $visitTime > $availableMinutes) {continue;
                     }// skip destinasi ini
                $distanceScore = max(0, 1 - ($dist / 15));
                $bayesianScore = $candidate->bayesian_score ?? 0;

                // Diversity bonus: kategori yang belum muncul dapat bonus
                $kategoriNama  = $candidate->kategori->nama_kategori ?? 'default';
                $categoryCount = count(array_filter($visitedCategories, fn($k) => $k === $kategoriNama));
                $diversityBonus = $categoryCount === 0 ? 0.50 : ($categoryCount === 1 ? 0.00 : -0.50);

                $combinedScore = ($distanceScore * 0.35)
                               + ($bayesianScore  * 0.30)
                               + ($diversityBonus * 0.35);

                if ($combinedScore > $bestScore) {
                    $bestScore    = $combinedScore;
                    $bestNext     = $candidate;
                    $bestDistance = $dist;
                }
            }

            if (!$bestNext) break;

            $travelMins = $this->haversine->estimateTravelTime($bestDistance);
            $visitMins  = $this->getVisitDuration($bestNext);

            $visited[]             = $bestNext->id;
            \Log::info('Stop ' . $stopNumber . ' dipilih: ' . $bestNext->nama_destinasi . 
                ' | Kategori: ' . ($bestNext->kategori->nama_kategori ?? 'null') .
                ' | Score: ' . round($bestScore, 3) .
                ' | visitedCategories: ' . implode(', ', $visitedCategories));
            $visitedCategories[]   = $bestNext->kategori->nama_kategori ?? 'default';
            $route[]               = $this->buildStop($bestNext, $stopNumber, $bestDistance, $travelMins);

            $totalDistance += $bestDistance;
            $totalMinutes  += $travelMins + $visitMins;
            $currentLat     = (float) $bestNext->latitude;
            $currentLon     = (float) $bestNext->longitude;
            $currentId      = $bestNext->id;
            $stopNumber++;
        }

        return [
            'route'          => $route,
            'total_distance' => round($totalDistance, 2),
            'total_minutes'  => $totalMinutes,
            'stop_count'     => count($route),
            'origin'         => ['lat' => $originLat, 'lon' => $originLon],
        ];
    }

    private function buildStop($dest, int $order, float $distance = 0, int $travelMins = 0): array
    {
        $foto = $dest->foto;
        if (is_string($foto)) {
            $decoded = json_decode($foto, true);
            $foto    = is_array($decoded) ? $decoded : [$foto];
        }

        return [
            'order'              => $order,
            'id'                 => $dest->id,
            'nama'               => $dest->nama_destinasi,
            'kategori'           => $dest->kategori->nama_kategori ?? 'Wisata',
            'latitude'           => (float) $dest->latitude,
            'longitude'          => (float) $dest->longitude,
            'harga'              => $dest->harga,
            'foto'               => $foto,
            'deskripsi'          => $dest->deskripsi,
            'is_featured'        => $dest->is_featured,
            'bayesian_score'     => round($dest->bayesian_score ?? 0, 3),
            'score_breakdown'    => $dest->score_breakdown ?? [],
            'distance_from_prev' => round($distance, 2),
            'travel_minutes'     => $travelMins,
            'visit_duration'     => $this->getVisitDuration($dest),
        ];
    }

    private function getVisitDuration($dest): int
    {
        $kategori = $dest->kategori->nama_kategori ?? 'default';
        return $this->visitDurationByCategory[$kategori]
            ?? $this->visitDurationByCategory['default'];
    }
}