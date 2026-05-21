<?php

namespace App\Services;

use App\Models\Destinasi;
use Illuminate\Support\Collection;

/**
 * Step 1: Content-Based Filtering
 * Filter destinasi berdasarkan kategori, harga (budget), dan status aktif.
 * Mengembalikan kandidat yang relevan sebelum di-rank.
 */
class ContentFilterService
{
    /**
     * @param array  $kategoriIds  ID kategori yang dipilih user (bisa multiple)
     * @param float  $budget       Budget maksimum per orang
     * @param string $tanggal      Tanggal kunjungan (untuk future: cek event/seasonal)
     * @return Collection<Destinasi>
     */
    public function filter(array $kategoriIds, float $budget, string $tanggal): Collection
    {
        return Destinasi::query()
            ->where('status', 'active')
            ->whereIn('kategori_id', $kategoriIds)
            ->where('harga', '<=', $budget)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['kategori', 'ulasan'])
            ->get();
    }

    /**
     * Filter lebih lanjut berdasarkan tipe companion (keluarga, pasangan, solo, grup)
     * Setiap tipe punya bobot kategori yang berbeda.
     */
    public function adjustForCompanion(Collection $candidates, string $companion): Collection
    {
        $companionWeights = [
            'keluarga' => [
                'preferred' => ['Taman', 'Pantai', 'Edukasi', 'Kuliner'],
                'avoid'     => ['Nightlife', 'Bar'],
            ],
            'pasangan' => [
                'preferred' => ['Pantai', 'Kuliner', 'Taman', 'Hiburan'],
                'avoid'     => [],
            ],
            'solo' => [
                'preferred' => ['Edukasi', 'Alam', 'Budaya', 'Kuliner'],
                'avoid'     => [],
            ],
            'grup' => [
                'preferred' => ['Hiburan', 'Taman', 'Pantai', 'Kuliner'],
                'avoid'     => ['Spa', 'Relaksasi'],
            ],
        ];

        $weights = $companionWeights[$companion] ?? ['preferred' => [], 'avoid' => []];

        // Tandai destinasi dengan skor afinitas companion
        return $candidates->map(function ($dest) use ($weights) {
            $kategoriNama = $dest->kategori->nama ?? '';
            $affinityBonus = 0;

            if (in_array($kategoriNama, $weights['preferred'])) {
                $affinityBonus = 0.15; // +15% skor
            }
            if (in_array($kategoriNama, $weights['avoid'])) {
                $affinityBonus = -0.30; // penalti
            }

            $dest->companion_affinity = $affinityBonus;
            return $dest;
        })->filter(fn($d) => $d->companion_affinity > -0.25); // buang yang sangat tidak cocok
    }
}
