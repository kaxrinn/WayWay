<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\Destinasi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaybotService
{
    // Pertanyaan preferensi yang akan ditanyakan secara berurutan
    const PREFERENCE_QUESTIONS = [
        'travel_type' => [
            'question' => 'Tipe wisata apa yang paling kamu suka?',
            'options'  => ['🏖️ Pantai & Alam', '🍽️ Kuliner', '🛍️ Belanja', '🏨 Hotel & Staycation', '🎭 Budaya & Sejarah', '🎉 Hiburan & Nightlife'],
        ],
        'budget' => [
            'question' => 'Berapa kisaran budget kamu per destinasi?',
            'options'  => ['💸 Gratis aja', '🪙 Di bawah Rp 50.000', '💰 Rp 50.000 – 200.000', '💎 Di atas Rp 200.000'],
        ],
        'companion' => [
            'question' => 'Lagi traveling bareng siapa nih?',
            'options'  => ['🧍 Solo trip', '💑 Pasangan', '👨‍👩‍👧 Keluarga + anak', '👥 Rombongan'],
        ],
    ];

    // Intent keywords yang trigger mode rekomendasi
    const RECOMMENDATION_KEYWORDS = [
        'rekomendasi', 'recommend', 'saran', 'suggest', 'mau ke mana', 'mau kemana',
        'destinasi apa', 'tempat wisata', 'wisata apa', 'where to go', 'what to visit',
        'mau jalan', 'mau liburan', 'liburan di batam', 'wisata batam',
    ];

    public function __construct(
        private EmbeddingService $embeddingService
    ) {}

    /**
     * Entry point utama - proses pesan dari user.
     */
    public function processMessage(ChatSession $session, string $userMessage): array
    {
        // Simpan pesan user
        ChatMessage::create([
            'session_id' => $session->id,
            'role'       => 'user',
            'content'    => $userMessage,
        ]);

        // Deteksi intent dan tentukan flow
        $intent = $this->detectIntent($userMessage, $session);

        $response = match ($intent) {
            'eliciting'     => $this->handleEliciting($session, $userMessage),
            'recommending'  => $this->handleRecommending($session, $userMessage),
            'general'       => $this->handleGeneral($session, $userMessage),
            default         => $this->handleGreeting($session, $userMessage),
        };

        // Simpan respons assistant
        ChatMessage::create([
            'session_id'       => $session->id,
            'role'             => 'assistant',
            'content'          => $response['message'],
            'context_destinasi'=> $response['context_destinasi'] ?? null,
        ]);

        return $response;
    }

    /**
     * Deteksi intent dari pesan user.
     */
    private function detectIntent(string $message, ChatSession $session): string
    {
        $msgLower = strtolower($message);
        $stage    = $session->stage;
        $prefs    = $session->preferences ?? [];

        // Jika sedang dalam proses eliciting, lanjutkan
        if ($stage === 'eliciting') {
            return 'eliciting';
        }

        // Cek keyword rekomendasi
        foreach (self::RECOMMENDATION_KEYWORDS as $keyword) {
            if (str_contains($msgLower, $keyword)) {
                return 'eliciting'; // mulai tanya preferensi dulu
            }
        }

        // Jika preferensi sudah lengkap dan user kirim sesuatu, anggap tanya umum
        return 'general';
    }

    /**
     * Handle sapaan awal.
     */
    private function handleGreeting(ChatSession $session, string $message): array
    {
        $session->update(['stage' => 'greeting']);

        $reply = $this->callGPT($session, $message, $this->systemPrompt());

        return ['message' => $reply, 'type' => 'text'];
    }

    /**
     * Handle proses tanya preferensi (eliciting).
     * Tanya satu per satu sampai semua pertanyaan terjawab.
     */
    private function handleEliciting(ChatSession $session, string $userMessage): array
    {
        $prefs = $session->preferences ?? [];

        // Simpan jawaban dari pertanyaan sebelumnya
        $unanswered = $this->getUnansweredPreference($prefs);
        $answered   = $this->extractAnswer($unanswered, $userMessage, $prefs);

        if ($answered) {
            $prefs = $answered;
            $session->update([
                'preferences' => $prefs,
                'stage'       => 'eliciting',
            ]);
        }

        // Cek apakah masih ada pertanyaan yang belum dijawab
        $nextKey = $this->getUnansweredKey($prefs);

        if ($nextKey) {
            $q = self::PREFERENCE_QUESTIONS[$nextKey];
            $optionsText = implode(' / ', $q['options']);

            $msg = $q['question'] . "\n\n" . $optionsText;

            return [
                'message' => $msg,
                'type'    => 'options',
                'options' => $q['options'],
                'pref_key'=> $nextKey,
            ];
        }

        // Semua preferensi sudah terkumpul → kasih rekomendasi
        return $this->handleRecommending($session, $userMessage, $prefs);
    }

    /**
     * Handle pemberian rekomendasi berdasarkan preferensi.
     */
    private function handleRecommending(ChatSession $session, string $userMessage, ?array $prefs = null): array
    {
        $prefs = $prefs ?? $session->preferences ?? [];
        $session->update(['stage' => 'recommending']);

        // Bangun query semantik dari preferensi
        $semanticQuery = $this->buildSemanticQuery($prefs, $userMessage);

        // Filter berdasarkan preferensi
        $filters = $this->buildFilters($prefs);

        // Semantic search
        $results = $this->embeddingService->semanticSearch(
            query:    $semanticQuery,
            topK:     5,
            filters:  $filters,
            minScore: 0.2
        );

        if ($results->isEmpty()) {
            // Fallback tanpa filter
            $results = $this->embeddingService->semanticSearch($semanticQuery, 5);
        }

        // Tambahkan destinasi featured jika belum ada
        $destIds = $results->pluck('destinasi.id')->toArray();
        $featured = Destinasi::where('is_featured', true)
            ->where('status', 'active')
            ->whereNotIn('id', $destIds)
            ->take(2)
            ->get();

        // Bangun konteks untuk GPT
        $contextList   = $results->pluck('destinasi')->toArray();
        $contextIds    = $results->pluck('destinasi.id')->toArray();
        $contextText   = $this->buildContextText($results->pluck('destinasi'), $featured);

        // Panggil GPT dengan konteks destinasi
        $systemPrompt = $this->systemPrompt() . "\n\n" . $this->ragPrompt($contextText, $prefs);
        $reply        = $this->callGPT($session, $userMessage, $systemPrompt);

        return [
            'message'           => $reply,
            'type'              => 'recommendation',
            'context_destinasi' => $contextIds,
            'destinasi_cards'   => $results->pluck('destinasi')->map(fn($d) => [
                'id'    => $d->id,
                'nama'  => $d->nama_destinasi,
                'harga' => $d->harga,
                'foto'  => $d->foto ? json_decode($d->foto, true)[0] ?? null : null,
                'lat'   => $d->latitude,
                'lng'   => $d->longitude,
            ])->toArray(),
        ];
    }

    /**
     * Handle pertanyaan umum tentang destinasi (RAG tanpa preferensi).
     */
    private function handleGeneral(ChatSession $session, string $userMessage): array
    {
        $results = $this->embeddingService->semanticSearch($userMessage, 4);

        $contextText = $this->buildContextText($results->pluck('destinasi'));
        $systemPrompt = $this->systemPrompt() . "\n\n" . $this->ragPrompt($contextText);

        $reply = $this->callGPT($session, $userMessage, $systemPrompt);

        return [
            'message'           => $reply,
            'type'              => 'text',
            'context_destinasi' => $results->pluck('destinasi.id')->toArray(),
        ];
    }

    /**
     * Panggil OpenAI Chat Completions API.
     */
    private function callGPT(ChatSession $session, string $userMessage, string $systemPrompt): string
    {
        // Ambil riwayat chat (max 10 pesan terakhir untuk hemat token)
        $history = ChatMessage::where('session_id', $session->id)
            ->orderBy('id', 'desc')
            ->take(10)
            ->get()
            ->reverse()
            ->map(fn($m) => [
                'role'    => $m->role,
                'content' => $m->content,
            ])
            ->values()
            ->toArray();

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ...$history,
            ['role' => 'user', 'content' => $userMessage],
        ];

        $response = Http::withToken(config('services.openai.key'))
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'       => 'gpt-4o',
                'messages'    => $messages,
                'max_tokens'  => 800,
                'temperature' => 0.7,
            ]);

        if (! $response->successful()) {
            Log::error('OpenAI Chat error', ['body' => $response->body()]);
            return 'Maaf, Waybot lagi ada gangguan teknis. Coba lagi ya! 🙏';
        }

        return $response->json('choices.0.message.content', 'Maaf, aku tidak mengerti. Coba tanya dengan cara lain ya!');
    }

    /**
     * System prompt utama Waybot.
     */
    private function systemPrompt(): string
    {
        return <<<PROMPT
Kamu adalah Waybot, asisten wisata cerdas dari aplikasi WayWay — platform penemuan destinasi wisata di Batam, Kepulauan Riau, Indonesia.
Kamu bisa berkomunikasi dalam Bahasa Indonesia dan English.
Deteksi bahasa yang digunakan user dan balas dengan bahasa yang sama.
Jika user pakai Indonesia → balas Indonesia.
Jika user pakai English → balas English.

Kepribadianmu:
- Ramah, hangat, dan sedikit santai tapi tetap informatif
- Berbicara seperti teman yang tahu banyak soal Batam
- Gunakan bahasa Indonesia yang natural, boleh sesekali pakai kata informal
- Gunakan emoji secukupnya untuk membuat percakapan lebih hidup, tapi jangan berlebihan

Yang bisa kamu bantu:
- Memberikan rekomendasi destinasi wisata di Batam sesuai preferensi
- Menjawab pertanyaan tentang destinasi (lokasi, harga, jam buka, aktivitas)
- Membantu merencanakan itinerary
- Info kuliner, hotel, dan aktivitas di Batam

Aturan penting:
- HANYA bahas destinasi di Batam dan sekitarnya (Kepulauan Riau)
- Jika ditanya di luar topik wisata Batam, tolak dengan sopan dan arahkan kembali
- Selalu jujur jika tidak tahu informasinya
- Jangan mengarang fakta tentang destinasi yang tidak ada di konteks
- Harga dan info yang kamu berikan berdasarkan data yang tersedia
PROMPT;
    }

    /**
     * Tambahan prompt dengan konteks destinasi dari RAG.
     */
    private function ragPrompt(string $contextText, ?array $prefs = null): string
    {
        $prefText = '';
        if ($prefs) {
            $prefText = "\nPreferensi wisatawan: " . json_encode($prefs, JSON_UNESCAPED_UNICODE);
        }

        return <<<PROMPT
{$prefText}

Berikut data destinasi yang relevan dari database WayWay. Gunakan informasi ini sebagai dasar rekomendasimu:

{$contextText}

Berikan rekomendasi yang personal, spesifik, dan menarik. Sebutkan nama destinasi, kenapa cocok untuk user ini, harga, dan satu keunikan yang menonjol. Format dengan rapi menggunakan emoji dan baris baru agar mudah dibaca.
PROMPT;
    }

    /**
     * Bangun teks konteks dari list destinasi untuk dimasukkan ke prompt.
     */
    private function buildContextText($destinasiCollection, $featured = null): string
    {
        $lines = [];

        foreach ($destinasiCollection as $dest) {
            $harga = $dest->harga > 0
                ? 'Rp ' . number_format($dest->harga, 0, ',', '.')
                : 'Gratis';

            $featuredTag = $dest->is_featured ? ' ⭐ [FEATURED]' : '';

            $lines[] = "---\n"
                . "Nama: {$dest->nama_destinasi}{$featuredTag}\n"
                . "Kategori: " . ($dest->kategori?->nama_kategori ?? '-') . "\n"
                . "Harga: {$harga}\n"
                . "Koordinat: {$dest->latitude}, {$dest->longitude}\n"
                . "Deskripsi: {$dest->deskripsi}";
        }

        if ($featured && $featured->count() > 0) {
            foreach ($featured as $dest) {
                $harga = $dest->harga > 0
                    ? 'Rp ' . number_format($dest->harga, 0, ',', '.')
                    : 'Gratis';

                $lines[] = "---\n"
                    . "Nama: {$dest->nama_destinasi} ⭐ [FEATURED - PRIORITAS TINGGI]\n"
                    . "Kategori: " . ($dest->kategori?->nama_kategori ?? '-') . "\n"
                    . "Harga: {$harga}\n"
                    . "Deskripsi: {$dest->deskripsi}";
            }
        }

        return implode("\n\n", $lines);
    }

    /**
     * Bangun query semantik dari preferensi user.
     */
    private function buildSemanticQuery(array $prefs, string $originalMessage): string
    {
        $parts = [$originalMessage];

        if (! empty($prefs['travel_type'])) {
            $parts[] = "wisata " . $prefs['travel_type'];
        }
        if (! empty($prefs['companion'])) {
            $parts[] = "cocok untuk " . $prefs['companion'];
        }

        return implode(', ', $parts) . ' di Batam';
    }

    /**
     * Bangun filter DB dari preferensi user.
     */
    private function buildFilters(array $prefs): array
    {
        $filters = [];

        if (! empty($prefs['budget'])) {
            $budget = $prefs['budget'];
            if (str_contains($budget, 'Gratis')) {
                $filters['max_harga'] = 0;
            } elseif (str_contains($budget, '50.000')) {
                $filters['max_harga'] = 50000;
            } elseif (str_contains($budget, '200.000')) {
                $filters['max_harga'] = 200000;
            }
            // Di atas 200rb = tidak ada filter harga
        }

        return $filters;
    }

    /**
     * Dapatkan key preferensi yang belum dijawab.
     */
    private function getUnansweredKey(array $prefs): ?string
    {
        foreach (array_keys(self::PREFERENCE_QUESTIONS) as $key) {
            if (empty($prefs[$key])) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Alias untuk getUnansweredKey.
     */
    private function getUnansweredPreference(array $prefs): ?string
    {
        return $this->getUnansweredKey($prefs);
    }

    /**
     * Ekstrak jawaban user dan simpan ke array preferences.
     */
    private function extractAnswer(?string $prefKey, string $userMessage, array $currentPrefs): ?array
    {
        if (! $prefKey) return null;

        $options = self::PREFERENCE_QUESTIONS[$prefKey]['options'] ?? [];
        $msgLower = strtolower($userMessage);

        // Coba match persis atau partial dengan opsi
        foreach ($options as $option) {
            // Hilangkan emoji dari opsi untuk matching
            $optionClean = trim(preg_replace('/[^\x{20}-\x{7E}]/u', '', $option));
            if (
                str_contains($msgLower, strtolower($optionClean)) ||
                str_contains($msgLower, strtolower($option))
            ) {
                $currentPrefs[$prefKey] = $option;
                return $currentPrefs;
            }
        }

        // Jika tidak ada match, simpan raw jawaban user
        $currentPrefs[$prefKey] = $userMessage;
        return $currentPrefs;
    }
}