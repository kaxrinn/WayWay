<?php

namespace App\Services;

use App\Models\Destinasi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    private string $apiKey;
    private string $model = 'text-embedding-3-small'; // 1536 dimensi, murah, cepat

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    /**
     * Buat teks yang akan di-embed dari data destinasi.
     * Makin kaya teksnya, makin relevan hasil semantic search-nya.
     */
    public function buildEmbeddingText(Destinasi $destinasi): string
    {
        $parts = [
            "Nama: {$destinasi->nama_destinasi}",
            "Kategori: " . ($destinasi->kategori?->nama_kategori ?? 'Umum'),
            "Deskripsi: {$destinasi->deskripsi}",
            "Harga tiket: " . ($destinasi->harga > 0
                ? 'Rp ' . number_format($destinasi->harga, 0, ',', '.')
                : 'Gratis'),
        ];

        return implode("\n", array_filter($parts));
    }

    /**
     * Generate embedding untuk satu destinasi dan simpan ke DB.
     */
    public function embedDestinasi(Destinasi $destinasi): void
    {
        $text = $this->buildEmbeddingText($destinasi);

        $vector = $this->getEmbedding($text);

        $destinasi->update([
            'embedding_text' => $text,
            'embedding'      => $vector,
            'embedded_at'    => now(),
        ]);
    }

    /**
     * Panggil OpenAI Embeddings API, kembalikan array float.
     */
    public function getEmbedding(string $text): array
    {
        $response = Http::withToken($this->apiKey)
            ->post('https://api.openai.com/v1/embeddings', [
                'model' => $this->model,
                'input' => $text,
            ]);

        if (! $response->successful()) {
            Log::error('OpenAI Embedding API error', ['body' => $response->body()]);
            throw new \RuntimeException('Gagal memanggil OpenAI Embedding API: ' . $response->body());
        }

        return $response->json('data.0.embedding');
    }

    /**
     * Cosine similarity antara dua vector.
     * Nilai 1.0 = identik, 0.0 = tidak relevan sama sekali.
     */
    public function cosineSimilarity(array $a, array $b): float
    {
        $dot      = 0.0;
        $normA    = 0.0;
        $normB    = 0.0;

        foreach ($a as $i => $valA) {
            $valB  = $b[$i] ?? 0.0;
            $dot  += $valA * $valB;
            $normA += $valA * $valA;
            $normB += $valB * $valB;
        }

        if ($normA === 0.0 || $normB === 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    /**
     * Cari destinasi yang paling relevan berdasarkan query teks.
     * Menggunakan cosine similarity antara embedding query dan embedding destinasi.
     *
     * @param  string   $query
     * @param  int      $topK         Jumlah hasil teratas
     * @param  array    $filters      Filter tambahan: ['kategori_id' => ..., 'max_harga' => ...]
     * @param  float    $minScore     Minimum similarity score (0-1)
     * @return \Illuminate\Support\Collection
     */
    public function semanticSearch(
        string $query,
        int    $topK    = 5,
        array  $filters = [],
        float  $minScore = 0.25
    ): \Illuminate\Support\Collection {

        $queryVector = $this->getEmbedding($query);

        // Ambil destinasi aktif yang sudah di-embed
        $dbQuery = Destinasi::where('status', 'active')
            ->whereNotNull('embedding')
            ->with('kategori');

        // Terapkan filter jika ada
        if (! empty($filters['kategori_id'])) {
            $dbQuery->where('kategori_id', $filters['kategori_id']);
        }
        if (isset($filters['max_harga'])) {
            $dbQuery->where('harga', '<=', $filters['max_harga']);
        }
        if (! empty($filters['is_featured'])) {
            $dbQuery->where('is_featured', true);
        }

        $destinasi = $dbQuery->get();

        // Hitung similarity untuk setiap destinasi
        $results = $destinasi->map(function ($dest) use ($queryVector) {
            $embedding = is_string($dest->embedding)
                ? json_decode($dest->embedding, true)
                : $dest->embedding;

            if (! $embedding) return null;

            $score = $this->cosineSimilarity($queryVector, $embedding);

            return [
                'destinasi' => $dest,
                'score'     => $score,
            ];
        })
        ->filter(fn($item) => $item && $item['score'] >= $minScore)
        ->sortByDesc('score')
        ->take($topK)
        ->values();

        return $results;
    }

    /**
     * Hitung jarak (km) antara dua koordinat menggunakan Haversine formula.
     */
    public function haversineDistance(
        float $lat1, float $lon1,
        float $lat2, float $lon2
    ): float {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}