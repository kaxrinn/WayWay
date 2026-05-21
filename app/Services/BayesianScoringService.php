<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * Step 2: Bayesian Scoring
 *
 * Formula Bayesian Average:
 *   score = (C * m + Σ ratings) / (C + n)
 *
 *   C = confidence weight (jumlah minimum review yang kita "percaya")
 *   m = global mean rating
 *   n = jumlah review destinasi ini
 *
 * Mengapa Bayesian? Karena destinasi baru dengan 2 rating bintang 5
 * tidak lebih baik dari destinasi veteran dengan 200 rating rata-rata 4.7.
 * Bayesian menarik skor ke rata-rata global jika data review sedikit.
 */
class BayesianScoringService
{
    // Berapa review "virtual" yang kita suntikkan sebagai prior
    private int $confidenceWeight = 10;

    public function rankCandidates(Collection $candidates): Collection
    {
        // Hitung global mean dari semua kandidat
        $globalMean = $candidates->avg(fn($d) => $d->average_rating ?? 3.5);
        $C = $this->confidenceWeight;
        $m = $globalMean ?: 3.5;

        return $candidates->map(function ($dest) use ($C, $m) {
            $n           = $dest->ulasan->count();
            $userRating  = $dest->ulasan->avg('rating') ?? $m;

            // Core Bayesian formula
            $bayesianScore = ($C * $m + $n * $userRating) / ($C + $n);

            // Faktor tambahan untuk boosting
            $featuredBonus   = $dest->is_featured ? 0.10 : 0;
            $companionAffinity = $dest->companion_affinity ?? 0;
            $priceScore      = $this->scorePriceValue($dest->harga);

            // Composite final score (0-1 scale)
            $dest->bayesian_score = min(1.0,
                ($bayesianScore / 5.0 * 0.50) // 50% dari Bayesian rating
                + ($priceScore * 0.20)          // 20% value for money
                + ($featuredBonus * 0.15)        // 15% featured boost
                + ($companionAffinity * 0.15)    // 15% companion fit
            );

            $dest->score_breakdown = [
                'bayesian'  => round($bayesianScore, 3),
                'price'     => round($priceScore, 3),
                'featured'  => $featuredBonus,
                'companion' => round($companionAffinity, 3),
                'final'     => round($dest->bayesian_score, 3),
            ];

            return $dest;
        })->sortByDesc('bayesian_score')->values();
    }

    /**
     * Score value-for-money: semakin murah relatif terhadap budget, semakin tinggi.
     * Destinasi gratis/murah dapat bonus, bukan penalti.
     */
    private function scorePriceValue(float $harga): float
    {
        // Normalisasi harga ke 0-1 (terbalik: murah = skor tinggi)
        $maxHarga = 500000; // cap di 500rb
        return max(0, 1 - ($harga / $maxHarga));
    }
}
