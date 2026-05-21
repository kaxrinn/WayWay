<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * Step 3: Haversine Distance Calculator
 *
 * Formula Haversine menghitung jarak "as the crow flies" antara dua titik
 * di permukaan bumi (koordinat GPS), memperhitungkan kelengkungan bumi.
 *
 *   a = sin²(Δlat/2) + cos(lat1) * cos(lat2) * sin²(Δlon/2)
 *   c = 2 * atan2(√a, √(1−a))
 *   d = R * c   (R = 6371 km)
 *
 * Digunakan untuk:
 * 1. Menghitung jarak dari titik awal user ke setiap destinasi
 * 2. Menghitung jarak antar destinasi (matrix) untuk Greedy Router
 */
class HaversineService
{
    private const EARTH_RADIUS_KM = 6371;

    /**
     * Hitung jarak antara dua koordinat GPS (dalam km)
     */
    public function distance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_KM * $c;
    }

    /**
     * Tambahkan jarak dari titik awal ke setiap destinasi
     * (digunakan untuk sorting awal)
     */
    public function attachDistances(
        Collection $destinations,
        float $originLat,
        float $originLon
    ): Collection {
        return $destinations->map(function ($dest) use ($originLat, $originLon) {
            $dest->distance_from_origin = $this->distance(
                $originLat, $originLon,
                (float) $dest->latitude, (float) $dest->longitude
            );
            return $dest;
        });
    }

    /**
     * Build distance matrix (n×n) antar semua destinasi terpilih.
     * Digunakan oleh GreedyRouterService untuk menyusun rute optimal.
     *
     * @return array<int, array<int, float>>  Matrix indexed by destinasi ID
     */
    public function buildDistanceMatrix(Collection $destinations): array
    {
        $matrix = [];
        $items  = $destinations->values();

        foreach ($items as $i => $from) {
            foreach ($items as $j => $to) {
                $fromId = $from->id;
                $toId   = $to->id;

                if ($i === $j) {
                    $matrix[$fromId][$toId] = 0;
                    continue;
                }

                $matrix[$fromId][$toId] = $this->distance(
                    (float) $from->latitude, (float) $from->longitude,
                    (float) $to->latitude,   (float) $to->longitude
                );
            }
        }

        return $matrix;
    }

    /**
     * Estimasi waktu tempuh (menit) berdasarkan jarak
     * Asumsi kecepatan rata-rata kota Batam: 40 km/h
     */
    public function estimateTravelTime(float $distanceKm): int
    {
        $speedKmh = 40;
        return (int) ceil(($distanceKm / $speedKmh) * 60);
    }
}
