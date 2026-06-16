<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Step 5: OSRM Route Validation
 *
 * OSRM (Open Source Routing Machine) — engine routing open-source berbasis OpenStreetMap.
 * Digunakan untuk validasi rute jalan NYATA (bukan garis lurus Haversine).
 *
 * API OSRM public: http://router.project-osrm.org
 * Endpoint: /route/v1/driving/{coordinates}
 *
 * Response berisi:
 * - distance: jarak jalan nyata (meter)
 * - duration: waktu tempuh nyata (detik)
 * - geometry: polyline untuk ditampilkan di peta
 *
 * Fallback: jika OSRM gagal, gunakan estimasi Haversine × 1.3 (faktor jalan memutar)
 */
class OsrmService
{
    private string $baseUrl;
    private bool $useOsrm;

    public function __construct()
    {
        // Bisa ganti ke self-hosted OSRM atau Google Maps Directions API
        $this->baseUrl = config('services.osrm.url', 'http://router.project-osrm.org');
        $this->useOsrm = config('services.osrm.enabled', true);
    }

    /**
     * Validasi dan enrichment rute dengan data jalan nyata.
     *
     * @param array $route  Output dari GreedyRouterService
     * @param array $origin ['lat' => ..., 'lon' => ...]
     * @return array  Rute yang sudah di-enriched dengan data OSRM
     */
    public function validateRoute(array $routeData): array
    {
        $stops = $routeData['route'];

        if (count($stops) < 2) {
            return $routeData; // Tidak perlu routing kalau cuma 1 destinasi
        }

        // Susun koordinat: origin → stop1 → stop2 → ... → stopN
        $origin = $routeData['origin'];
        $coordinates = ["{$origin['lon']},{$origin['lat']}"];

        foreach ($stops as $stop) {
            $coordinates[] = "{$stop['longitude']},{$stop['latitude']}";
        }

        $coordString = implode(';', $coordinates);

        if (!$this->useOsrm) {
            return $this->fallbackEstimate($routeData);
        }

        try {
            $response = Http::timeout(8)
                ->get("{$this->baseUrl}/route/v1/driving/{$coordString}", [
                    'overview'    => 'full',
                    'geometries'  => 'geojson',
                    'steps'       => 'true',
                    'annotations' => 'false',
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['code'] === 'Ok' && !empty($data['routes'])) {
                    return $this->enrichRouteWithOsrm($routeData, $data['routes'][0]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('OSRM request failed, using Haversine fallback', [
                'error' => $e->getMessage()
            ]);
        }

        return $this->fallbackEstimate($routeData);
    }

    /**
     * Enrich data rute dengan response OSRM
     */
    private function enrichRouteWithOsrm(array $routeData, array $osrmRoute): array
    {
        $legs = $osrmRoute['legs'] ?? [];

        // OSRM memberikan data per "leg" (antar titik)
        // Leg index 0 = origin → stop1, leg 1 = stop1 → stop2, dst
        foreach ($routeData['route'] as $index => &$stop) {
            $legIndex = $index; // origin ke stop pertama = leg[0]
            if (isset($legs[$legIndex])) {
                $leg = $legs[$legIndex];
                $stop['road_distance_km'] = round($leg['distance'] / 1000, 2);
                $stop['road_duration_min'] = (int) ceil($leg['duration'] / 60);
                $stop['road_validated'] = true;

                // Update travel time dengan data nyata
                $stop['travel_minutes'] = $stop['road_duration_min'];
            }
        }
        unset($stop);

        // Update total distance dengan data OSRM
        $routeData['total_distance']    = round($osrmRoute['distance'] / 1000, 2);
        $routeData['total_road_minutes'] = (int) ceil($osrmRoute['duration'] / 60);
        $routeData['osrm_geometry'] = $osrmRoute['geometry'] ?? null; // GeoJSON polyline
        $routeData['osrm_validated']    = true;

        return $routeData;
    }

    /**
     * Fallback: estimasi jarak jalan = Haversine × faktor koreksi
     * Faktor 1.3 adalah rata-rata rasio jalan vs garis lurus di kota
     */
    private function fallbackEstimate(array $routeData): array
    {
        $correctionFactor = 1.3;

        foreach ($routeData['route'] as &$stop) {
            $stop['road_distance_km']  = round($stop['distance_from_prev'] * $correctionFactor, 2);
            $stop['road_duration_min'] = (int) ceil($stop['travel_minutes'] * $correctionFactor);
            $stop['road_validated']    = false;
        }
        unset($stop);

        $routeData['total_distance']    = round($routeData['total_distance'] * $correctionFactor, 2);
        $routeData['osrm_validated']    = false;
        $routeData['fallback_reason']   = 'OSRM unavailable, using Haversine × 1.3 estimate';

        return $routeData;
    }

    /**
     * Alternatif: Google Maps Directions API
     * Aktifkan jika punya Google Maps API Key
     */
    public function validateWithGoogleMaps(array $routeData): array
    {
        $apiKey = config('services.google_maps.key');

        if (!$apiKey) {
            return $this->fallbackEstimate($routeData);
        }

        $stops  = $routeData['route'];
        $origin = $routeData['origin'];

        $waypoints = collect($stops)->map(fn($s) => "{$s['latitude']},{$s['longitude']}")->implode('|');

        try {
            $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/directions/json', [
                'origin'      => "{$origin['lat']},{$origin['lon']}",
                'destination' => end($stops)['latitude'] . ',' . end($stops)['longitude'],
                'waypoints'   => "optimize:true|{$waypoints}",
                'key'         => $apiKey,
            ]);

            if ($response->successful() && $response->json('status') === 'OK') {
                // Parse Google Maps response...
                // (implementasi detail disesuaikan dengan response structure Google)
                $legs = $response->json('routes.0.legs') ?? [];
                return $this->enrichFromGoogleLegs($routeData, $legs);
            }
        } catch (\Exception $e) {
            Log::warning('Google Maps API failed', ['error' => $e->getMessage()]);
        }

        return $this->fallbackEstimate($routeData);
    }

    private function enrichFromGoogleLegs(array $routeData, array $legs): array
    {
        foreach ($routeData['route'] as $i => &$stop) {
            if (isset($legs[$i])) {
                $stop['road_distance_km']  = round($legs[$i]['distance']['value'] / 1000, 2);
                $stop['road_duration_min'] = (int) ceil($legs[$i]['duration']['value'] / 60);
                $stop['road_validated']    = true;
                $stop['travel_minutes']    = $stop['road_duration_min'];
            }
        }
        unset($stop);

        $routeData['osrm_validated'] = true;
        return $routeData;
    }
}
