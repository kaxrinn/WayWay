<?php

namespace App\Services;

use App\Models\Destinasi;
use Illuminate\Support\Collection;

class ContentFilterService
{
    public function __construct(private HaversineService $haversine) {}

    public function filter(
        array $kategoriIds,
        float $budget,
        string $tanggal,
        float $originLat = 1.1296758,
        float $originLon = 104.0452254,
        float $radiusKm  = 15.0
    ): Collection {
        $all = Destinasi::query()
            ->where('status', 'active')
            ->whereIn('kategori_id', $kategoriIds)
            ->where('harga', '<=', $budget)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['kategori', 'ulasan'])
            ->get();

        // Filter hanya destinasi dalam radius dari origin
        return $all->filter(function ($dest) use ($originLat, $originLon, $radiusKm) {
            $jarak = $this->haversine->distance(
                $originLat, $originLon,
                (float) $dest->latitude,
                (float) $dest->longitude
            );
            return $jarak <= $radiusKm;
        });
    }

    public function adjustForCompanion(Collection $candidates, string $companion): Collection
    {
        $companionWeights = [
            'keluarga' => [
                'preferred' => ['Beaches', 'Man-Made Attractions', 'Cultural & Heritage Sites', 'Restaurants', 'Local Eateries'],
                'avoid'     => ['Nightlife'],
            ],
            'pasangan' => [
                'preferred' => ['Beaches', 'Restaurants', 'Coffee Shops', 'Cultural & Heritage Sites', 'Salon & SPA'],
                'avoid'     => [],
            ],
            'solo' => [
                'preferred' => ['Cultural & Heritage Sites', 'Nature & Eco Tourism', 'Coffee Shops', 'Local Eateries', 'Religious Sites'],
                'avoid'     => [],
            ],
            'grup' => [
                'preferred' => ['Man-Made Attractions', 'Beaches', 'Shopping Mall', 'Restaurants', 'Cinemas'],
                'avoid'     => ['Salon & SPA'],
            ],
        ];

        $weights = $companionWeights[$companion] ?? ['preferred' => [], 'avoid' => []];

        return $candidates->map(function ($dest) use ($weights) {
            $kategoriNama  = $dest->kategori->nama_kategori ?? '';
            $affinityBonus = 0;

            if (in_array($kategoriNama, $weights['preferred'])) {
                $affinityBonus = 0.15;
            }
            if (in_array($kategoriNama, $weights['avoid'])) {
                $affinityBonus = -0.30;
            }

            $dest->companion_affinity = $affinityBonus;
            return $dest;
        })->filter(fn($d) => $d->companion_affinity > -0.25);
    }
}