<?php

namespace App\Services;

use App\Models\Destinasi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    private string $apiKey;

    // Model embedding Gemini
    private string $model = 'text-embedding-004';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    /**
     * Build teks embedding dari destinasi
     */
    public function buildEmbeddingText(Destinasi $destinasi): string
    {
        $parts = [
            "Nama: {$destinasi->nama_destinasi}",
            "Kategori: " . ($destinasi->kategori?->nama_kategori ?? 'Umum'),
            "Deskripsi: {$destinasi->deskripsi}",
            "Harga tiket: " . (
                $destinasi->harga > 0
                    ? 'Rp ' . number_format($destinasi->harga, 0, ',', '.')
                    : 'Gratis'
            ),
        ];

        return implode("\n", array_filter($parts));
    }

    /**
     * Generate embedding & simpan
     */
    public function embedDestinasi(Destinasi $destinasi): void
    {
        $text = $this->buildEmbeddingText($destinasi);

        $vector = $this->getEmbedding($text);

        $destinasi->update([
            'embedding_text' => $text,
            'embedding'      => json_encode($vector),
            'embedded_at'    => now(),
        ]);
    }

    /**
     * Gemini Embedding API
     */
    public function getEmbedding(string $text): array
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:embedContent?key={$this->apiKey}";

        $response = Http::timeout(60)->post($url, [
            'model' => "models/{$this->model}",
            'content' => [
                'parts' => [
                    [
                        'text' => $text
                    ]
                ]
            ]
        ]);

        if (!$response->successful()) {

            Log::error('Gemini Embedding API Error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new \RuntimeException(
                'Gemini Embedding API Error: ' . $response->body()
            );
        }

        return $response->json('embedding.values') ?? [];
    }

    /**
     * Cosine similarity
     */
    public function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $i => $valA) {

            $valB = $b[$i] ?? 0.0;

            $dot += $valA * $valB;
            $normA += $valA * $valA;
            $normB += $valB * $valB;
        }

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    /**
     * Semantic search
     */
    public function semanticSearch(
        string $query,
        int $topK = 5,
        array $filters = [],
        float $minScore = 0.25
    ): \Illuminate\Support\Collection {

        $queryVector = $this->getEmbedding($query);

        $dbQuery = Destinasi::where('status', 'active')
            ->whereNotNull('embedding')
            ->with('kategori');

        if (!empty($filters['kategori_id'])) {
            $dbQuery->where('kategori_id', $filters['kategori_id']);
        }

        if (isset($filters['max_harga'])) {
            $dbQuery->where('harga', '<=', $filters['max_harga']);
        }

        if (!empty($filters['is_featured'])) {
            $dbQuery->where('is_featured', true);
        }

        $destinasi = $dbQuery->get();

        $results = $destinasi->map(function ($dest) use ($queryVector) {

            $embedding = is_string($dest->embedding)
                ? json_decode($dest->embedding, true)
                : $dest->embedding;

            if (!$embedding) {
                return null;
            }

            $score = $this->cosineSimilarity(
                $queryVector,
                $embedding
            );

            return [
                'destinasi' => $dest,
                'score' => $score,
            ];
        })
        ->filter(fn($item) =>
            $item && $item['score'] >= $minScore
        )
        ->sortByDesc('score')
        ->take($topK)
        ->values();

        return $results;
    }

    /**
     * Haversine distance
     */
    public function haversineDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {

        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1))
            * cos(deg2rad($lat2))
            * sin($dLon / 2) ** 2;

        return $earthRadius * 2
            * atan2(sqrt($a), sqrt(1 - $a));
    }
}