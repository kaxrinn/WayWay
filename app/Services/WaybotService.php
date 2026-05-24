<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\Destinasi;
use App\Models\Ulasan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaybotService
{
    const PREFERENCE_QUESTIONS = [
        'travel_type' => [
            'question' => "What kind of trip are you looking for? Pick the one that suits you best!",
            'options'  => [
                '🏖️ Beach & Nature',
                '🍽️ Food & Culinary',
                '🛍️ Shopping',
                '🏨 Hotel & Staycation',
                '🎭 Culture & History',
                '🎉 Entertainment & Nightlife',
            ],
        ],
        'budget' => [
            'question' => "What's your budget per destination?",
            'options'  => [
                '💸 Free only',
                '🪙 Under Rp 50,000',
                '💰 Rp 50,000 – 200,000',
                '💎 Above Rp 200,000',
            ],
        ],
        'companion' => [
            'question' => "Who are you traveling with?",
            'options'  => [
                '🧍 Solo trip',
                '💑 Partner / Couple',
                '👨‍👩‍👧 Family with kids',
                '👥 Group',
            ],
        ],
        'duration' => [
            'question' => "How many days are you planning to stay in Batam?",
            'options'  => [
                '📅 Just one day (day trip)',
                '📅 2 days',
                '📅 3–4 days',
                '📅 A week or more',
            ],
        ],
        'location' => [
            'question' => "Where are you right now in Batam? This helps me recommend the closest spots for you!",
            'options'  => [
                '📍 Batam Centre',
                '📍 Nagoya / Lubuk Baja',
                '📍 Nongsa',
                '📍 Batu Ampar',
                '📍 Sekupang',
                '📍 Not sure / Anywhere is fine',
                '📍 Use my GPS location',  // ← trigger GPS di frontend
            ],
        ],
    ];

    const RECOMMENDATION_KEYWORDS = [
        'recommendation', 'recommend', 'suggest', 'where to go', 'what to visit',
        'rekomendasi', 'saran', 'mau ke mana', 'mau kemana', 'destinasi apa',
        'tempat wisata', 'wisata apa', 'mau jalan', 'mau liburan', 'wisata batam',
        'kemana ya', 'enaknya kemana', 'rekomen', 'tempat bagus', 'worth it',
        'bisa kemana', 'where should i go', 'what should i visit', 'places to visit',
        'best place', 'good place', 'nice place',
    ];

    const MINIMUM_REVIEWS = 10;

    const AREA_COORDINATES = [
        'Batam Centre'  => ['lat' => 1.1218,  'lng' => 104.0529],
        'Nagoya'        => ['lat' => 1.1301,  'lng' => 104.0188],
        'Lubuk Baja'    => ['lat' => 1.1301,  'lng' => 104.0188],
        'Nongsa'        => ['lat' => 1.1800,  'lng' => 104.1200],
        'Batu Ampar'    => ['lat' => 1.1120,  'lng' => 104.0361],
        'Sekupang'      => ['lat' => 1.0900,  'lng' => 103.9800],
    ];

    // =============================================
    // ENTRY POINT
    // =============================================

    public function processMessage(ChatSession $session, string $userMessage, ?array $gpsCoords = null): array
    {
        // Kalau ada GPS coords dari frontend, simpan ke preferences
        if ($gpsCoords && !empty($gpsCoords['lat']) && !empty($gpsCoords['lng'])) {
            $prefs = $session->preferences ?? [];
            $prefs['location']     = 'GPS';
            $prefs['location_lat'] = $gpsCoords['lat'];
            $prefs['location_lng'] = $gpsCoords['lng'];
            $session->update(['preferences' => $prefs]);
        }

        ChatMessage::create([
            'session_id' => $session->id,
            'role'       => 'user',
            'content'    => $userMessage,
        ]);

        $intent = $this->detectIntent($userMessage, $session);

        $response = match ($intent) {
            'eliciting'    => $this->handleEliciting($session, $userMessage),
            'recommending' => $this->handleRecommending($session, $userMessage),
            'general'      => $this->handleGeneral($session, $userMessage),
            default        => $this->handleGreeting($session, $userMessage),
        };

        ChatMessage::create([
            'session_id'        => $session->id,
            'role'              => 'assistant',
            'content'           => $response['message'],
            'context_destinasi' => $response['context_destinasi'] ?? null,
        ]);

        return $response;
    }

    // =============================================
    // INTENT DETECTION
    // =============================================

    private function detectIntent(string $message, ChatSession $session): string
    {
        $msgLower = strtolower($message);
        $stage    = $session->stage;

        if ($stage === 'eliciting') return 'eliciting';

        foreach (self::RECOMMENDATION_KEYWORDS as $keyword) {
            if (str_contains($msgLower, $keyword)) return 'eliciting';
        }

        return 'general';
    }

    // =============================================
    // HANDLERS
    // =============================================

    private function handleGreeting(ChatSession $session, string $message): array
    {
        $session->update(['stage' => 'greeting']);
        $reply = $this->callGPT($session, $message, $this->systemPrompt());
        return ['message' => $reply, 'type' => 'text'];
    }

    private function handleEliciting(ChatSession $session, string $userMessage): array
    {
        $prefs      = $session->preferences ?? [];
        $unanswered = $this->getUnansweredKey($prefs);
        $answered   = $this->extractAnswer($unanswered, $userMessage, $prefs);

        if ($answered) {
            $prefs = $answered;
            $session->update(['preferences' => $prefs, 'stage' => 'eliciting']);
        }

        $nextKey = $this->getUnansweredKey($prefs);

        // Kalau pertanyaan selanjutnya adalah location dan user pilih GPS
        if ($nextKey === 'location') {
            $q = self::PREFERENCE_QUESTIONS[$nextKey];
            return [
                'message'     => $q['question'],
                'type'        => 'options',
                'options'     => $q['options'],
                'pref_key'    => $nextKey,
                'has_gps'     => true, // sinyal ke frontend untuk tampilkan tombol GPS
            ];
        }

        if ($nextKey) {
            $q = self::PREFERENCE_QUESTIONS[$nextKey];
            return [
                'message'  => $q['question'],
                'type'     => 'options',
                'options'  => $q['options'],
                'pref_key' => $nextKey,
                'has_gps'  => false,
            ];
        }

        return $this->handleRecommending($session, $userMessage, $prefs);
    }

    private function handleRecommending(ChatSession $session, string $userMessage, ?array $prefs = null): array
    {
        $prefs = $prefs ?? $session->preferences ?? [];
        $session->update(['stage' => 'recommending']);

        $destinasi = $this->fetchDestinasiBySqlFilter($prefs);

        if ($destinasi->isEmpty()) {
            $destinasi = Destinasi::where('status', 'active')
                ->with(['kategori', 'ulasan'])
                ->orderByDesc('is_featured')
                ->take(8)
                ->get();
        }

        $globalAvg  = $this->getGlobalAverageRating();
        $userCoords = $this->resolveUserCoordinates($prefs);

        $destinasi = $destinasi->map(function ($dest) use ($globalAvg, $userCoords) {
            $dest->bayesian_score = $this->bayesianRating(
                $dest->ulasan->avg('rating') ?? 0,
                $dest->ulasan->count(),
                $globalAvg
            );
            $dest->review_count = $dest->ulasan->count();
            $dest->avg_rating   = round($dest->ulasan->avg('rating') ?? 0, 1);
            $dest->jarak_km     = null;

            if ($userCoords && $dest->latitude && $dest->longitude) {
                $dest->jarak_km = round($this->haversineDistance(
                    $userCoords['lat'], $userCoords['lng'],
                    (float) $dest->latitude, (float) $dest->longitude
                ), 1);
            }

            return $dest;
        })->sortByDesc('bayesian_score')->values();

        $reply = $this->callGPT(
            $session,
            $userMessage,
            $this->systemPrompt() . "\n\n" . $this->fewShotPrompt($destinasi, $prefs)
        );

        return [
            'message'           => $reply,
            'type'              => 'recommendation',
            'context_destinasi' => $destinasi->pluck('id')->toArray(),
            'destinasi_cards'   => $destinasi->take(3)->map(fn($d) => [
                'id'     => $d->id,
                'nama'   => $d->nama_destinasi,
                'harga'  => $d->harga,
                'foto'   => $d->foto ? ($d->foto[0] ?? null) : null,
                'lat'    => $d->latitude,
                'lng'    => $d->longitude,
                'rating' => $d->avg_rating,
                'review' => $d->review_count,
                'jarak'  => $d->jarak_km,
            ])->toArray(),
        ];
    }

    private function handleGeneral(ChatSession $session, string $userMessage): array
    {
        $keyword   = $this->extractKeyword($userMessage);
        $destinasi = Destinasi::where('status', 'active')
            ->with(['kategori', 'ulasan'])
            ->where(function ($q) use ($keyword) {
                $q->where('nama_destinasi', 'like', "%{$keyword}%")
                  ->orWhere('deskripsi', 'like', "%{$keyword}%");
            })
            ->take(5)
            ->get();

        $globalAvg = $this->getGlobalAverageRating();
        $destinasi = $destinasi->map(function ($dest) use ($globalAvg) {
            $dest->bayesian_score = $this->bayesianRating(
                $dest->ulasan->avg('rating') ?? 0,
                $dest->ulasan->count(),
                $globalAvg
            );
            $dest->review_count = $dest->ulasan->count();
            $dest->avg_rating   = round($dest->ulasan->avg('rating') ?? 0, 1);
            $dest->jarak_km     = null;
            return $dest;
        })->sortByDesc('bayesian_score')->values();

        $reply = $this->callGPT(
            $session,
            $userMessage,
            $this->systemPrompt() . "\n\n" . $this->generalContextPrompt($destinasi)
        );

        return [
            'message'           => $reply,
            'type'              => 'text',
            'context_destinasi' => $destinasi->pluck('id')->toArray(),
        ];
    }

    // =============================================
    // SQL FILTER
    // =============================================

    private function fetchDestinasiBySqlFilter(array $prefs): \Illuminate\Support\Collection
    {
        $query = Destinasi::where('status', 'active')->with(['kategori', 'ulasan']);

        if (!empty($prefs['travel_type'])) {
            $kategoriMap = [
                'Beach'        => ['Beaches', 'Nature & Eco Tourism'],
                'Nature'       => ['Nature & Eco Tourism', 'Beaches'],
                'Food'         => ['Restaurants', 'Cafes', 'Food & Beverage'],
                'Culinary'     => ['Restaurants', 'Cafes', 'Food & Beverage'],
                'Shopping'     => ['Shopping Mall', 'Markets'],
                'Hotel'        => ['Hotels & Accommodation', 'Resorts'],
                'Staycation'   => ['Hotels & Accommodation', 'Resorts'],
                'Culture'      => ['Cultural & Heritage Sites', 'Museums'],
                'History'      => ['Cultural & Heritage Sites', 'Museums'],
                'Entertainment'=> ['Entertainment', 'Theme Parks'],
                'Nightlife'    => ['Night Entertainment', 'Entertainment'],
                // Indonesia fallback
                'Pantai'       => ['Beaches', 'Nature & Eco Tourism'],
                'Kuliner'      => ['Restaurants', 'Cafes', 'Food & Beverage'],
                'Belanja'      => ['Shopping Mall', 'Markets'],
                'Budaya'       => ['Cultural & Heritage Sites', 'Museums'],
                'Hiburan'      => ['Entertainment', 'Theme Parks'],
            ];

            $tipe = $prefs['travel_type'];
            foreach ($kategoriMap as $keyword => $kategoriNames) {
                if (stripos($tipe, $keyword) !== false) {
                    $query->whereHas('kategori', fn($q) => $q->whereIn('nama_kategori', $kategoriNames));
                    break;
                }
            }
        }

        if (!empty($prefs['budget'])) {
            $budget = $prefs['budget'];
            if (str_contains($budget, 'Free') || str_contains($budget, 'Gratis')) {
                $query->where('harga', 0);
            } elseif (str_contains($budget, '50,000') || str_contains($budget, '50.000')) {
                $query->where('harga', '<=', 50000);
            } elseif (str_contains($budget, '200,000') || str_contains($budget, '200.000')) {
                $query->where('harga', '<=', 200000);
            }
        }

        return $query->orderByDesc('is_featured')->take(8)->get();
    }

    // =============================================
    // BAYESIAN RATING
    // score = (n/(n+m)) × R + (m/(n+m)) × C
    // n = review count, m = minimum reviews (10)
    // R = destination avg rating, C = global avg rating
    // =============================================

    private function bayesianRating(float $avgRating, int $reviewCount, float $globalAvg): float
    {
        $m = self::MINIMUM_REVIEWS;
        if ($reviewCount === 0) return $globalAvg * 0.5;

        return ($reviewCount / ($reviewCount + $m)) * $avgRating
             + ($m / ($reviewCount + $m)) * $globalAvg;
    }

    private function getGlobalAverageRating(): float
    {
        return Ulasan::whereHas('destinasi', fn($q) => $q->where('status', 'active'))
            ->avg('rating') ?? 3.5;
    }

    // =============================================
    // HAVERSINE DISTANCE
    // =============================================

    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a    = sin($dLat / 2) ** 2
              + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Resolve user coordinates dari preferences.
     * Bisa dari GPS (lat/lng langsung) atau dari pilihan area.
     */
    private function resolveUserCoordinates(array $prefs): ?array
    {
        // Priority 1: GPS koordinat langsung dari browser
        if (!empty($prefs['location_lat']) && !empty($prefs['location_lng'])) {
            return [
                'lat' => (float) $prefs['location_lat'],
                'lng' => (float) $prefs['location_lng'],
            ];
        }

        // Priority 2: Pilihan area
        $location = $prefs['location'] ?? null;
        if (!$location) return null;

        foreach (self::AREA_COORDINATES as $area => $coords) {
            if (stripos($location, $area) !== false) return $coords;
        }

        return null;
    }

    // =============================================
    // FEW-SHOT PROMPT (dalam bahasa Inggris)
    // =============================================

    private function fewShotPrompt(\Illuminate\Support\Collection $destinasi, array $prefs): string
    {
        $dataDestinasi = $destinasi->take(8)->map(function ($dest) {
            $harga       = $dest->harga > 0 ? 'Rp ' . number_format($dest->harga, 0, ',', '.') : 'Free';
            $featured    = $dest->is_featured ? ' [FEATURED]' : '';
            $kategori    = $dest->kategori?->nama_kategori ?? 'General';
            $reviewCount = $dest->review_count;
            $avgRating   = $dest->avg_rating;
            $jarak       = $dest->jarak_km !== null ? "~{$dest->jarak_km} km from your location" : 'Distance unknown';

            if ($reviewCount === 0) {
                $ratingLabel = 'No reviews yet';
            } elseif ($reviewCount < self::MINIMUM_REVIEWS) {
                $ratingLabel = "Rated {$avgRating}/5 (only {$reviewCount} reviews — limited data)";
            } else {
                $ratingLabel = "Rated {$avgRating}/5 from {$reviewCount} reviews";
            }

            $sampleReviews = $dest->ulasan
                ->sortByDesc('created_at')
                ->take(2)
                ->pluck('komentar')
                ->filter()
                ->map(fn($k) => '"' . Str::limit($k, 80) . '"')
                ->join(', ');

            return "• {$dest->nama_destinasi}{$featured}\n"
                . "  Category: {$kategori} | Price: {$harga} | Distance: {$jarak}\n"
                . "  {$ratingLabel}\n"
                . ($sampleReviews ? "  Visitor says: {$sampleReviews}\n" : '')
                . "  About: " . Str::limit($dest->deskripsi, 150);
        })->join("\n\n");

        $tipeWisata = $prefs['travel_type'] ?? '-';
        $budget     = $prefs['budget']      ?? '-';
        $companion  = $prefs['companion']   ?? '-';
        $duration   = $prefs['duration']    ?? '-';
        $location   = $prefs['location']    ?? '-';
        $isGps      = $location === 'GPS' ? 'User\'s exact GPS location' : $location;

        return <<<PROMPT
DESTINATION DATA FROM WAYWAY DATABASE:
(Sorted by Bayesian rating — considering both quantity and value of reviews)

{$dataDestinasi}

---
TRAVELER PREFERENCES:
- Trip type      : {$tipeWisata}
- Budget         : {$budget}
- Traveling with : {$companion}
- Duration       : {$duration}
- Current location: {$isGps}

---
FEW-SHOT EXAMPLES — HOW TO RESPOND:

Example 1 — 3 recommendations, all with good reviews:
"Here are 3 spots I'd recommend for you!

1. **Nongsa Beach** 🌊
This one's a solid pick! Rated 4.5/5 from 87 reviews — clearly a crowd favorite. The beach is clean, the sand is white, and on a clear night you can see the lights of Singapore across the water. Entrance is only Rp10,000. It's about 18 km from Batam Centre, roughly 30 minutes. Pro tip: go in the morning or late afternoon — midday can get really hot and the tide tends to go out.

2. **Melur Beach** 🏖️
A quieter alternative if you want something more private. Rated 4.2/5 from 34 reviews. The water is crystal clear — great for snorkeling. Entrance is Rp15,000. About 12 km from your location. Tip: bring your own snacks since food stalls are limited there.

3. **Nongsa Point Marina** ☕
Perfect to wrap up the day after the beach. Sit back, have a coffee, and watch boats come and go. Rated 4.0/5 from 22 reviews. Drinks range from Rp25,000–50,000. The view during golden hour is stunning for photos!"

Example 2 — One destination has limited reviews:
"Here are 3 recommendations for you!

1. **Harbour Bay Mall** 🛍️
Top pick for shopping. Rated 4.3/5 from 95 reviews — well-established. Lots of local and international brands, plus a food court. Free entry. About 5 km from Nagoya.

2. **Mega Mall Batam Centre** 🛍️
Bigger with more variety. Rated 4.1/5 from 112 reviews. Free entry, and there's a cinema if you want to relax after shopping. Around 8 km from your location.

3. **Kepri Mall** 🛍️
This one has limited reviews — only 7 so far, so the data is still sparse. But those who've been say it's comfortable and less crowded, which is great if you hate the hustle. Could be a hidden gem worth checking out!"

Example 3 — Couple trip:
"For a romantic getaway, here's what I'd suggest:

1. **Batam View Beach Resort** 🌅
Perfect for couples. Rated 4.4/5 from 63 reviews. Stunning sunset views, private beach area, and cozy ambiance. Room rates start around Rp500,000/night. Worth every penny for the experience.

2. **Nongsa Point Marina** 🚤
Great for a relaxed evening together — sip cocktails while watching yachts sail by. Rated 4.0/5 from 22 reviews. Drinks around Rp50,000–100,000. Very romantic vibe at night.

3. **Ocarina Park** 🌳
Nice evening stroll for couples. Free entry! Rated 3.9/5 from 41 reviews. Lakeside park with fairy lights in the evening — lovely for a quiet walk together."

---
STRICT RULES — MUST FOLLOW:
1. ALWAYS recommend exactly 3 destinations — no more, no less
2. Format: number → destination name in bold → personal explanation
3. Mention distance from user's location for each destination
4. Be honest about reviews — if few reviews, say so but keep it positive
5. Give at least 1 practical tip per destination (best time to visit, what to watch out for, etc.)
6. Match tone to companion:
   - Partner/Couple → romantic, intimate, atmosphere-focused
   - Family with kids → safety, child-friendly facilities, fun for all ages
   - Solo → freedom, budget-friendly, adventure
   - Group → capacity, parking, group dining options
7. Consider duration — if it's a day trip, prioritize nearby destinations
8. Use natural, friendly language — like talking to a friend
9. Detect user language: if they write in Indonesian → reply in Indonesian, if English → reply in English
10. NEVER make up information that is not in the data above
PROMPT;
    }

    private function generalContextPrompt(\Illuminate\Support\Collection $destinasi): string
    {
        if ($destinasi->isEmpty()) {
            return "No specific destination data found for this query. Answer generally about Batam if you know, but remind the user to verify directly with the place.";
        }

        $dataText = $destinasi->map(function ($dest) {
            $harga      = $dest->harga > 0 ? 'Rp ' . number_format($dest->harga, 0, ',', '.') : 'Free';
            $ratingText = $dest->review_count > 0
                ? "Rated {$dest->avg_rating}/5 ({$dest->review_count} reviews)"
                : 'No reviews yet';

            return "• {$dest->nama_destinasi} | {$dest->kategori?->nama_kategori} | {$harga} | {$ratingText}\n"
                . "  " . Str::limit($dest->deskripsi, 200);
        })->join("\n\n");

        return <<<PROMPT
RELEVANT DESTINATION DATA FROM DATABASE:

{$dataText}

Answer the user's question based on the data above in a friendly and informative way. If certain information is not available in the data, be honest and suggest the user verify directly or contact the venue.
PROMPT;
    }

    // =============================================
    // SYSTEM PROMPT (English)
    // =============================================

    private function systemPrompt(): string
    {
        return <<<PROMPT
You are Waybot — a smart travel companion from WayWay, a tourism discovery platform in Batam, Riau Islands, Indonesia.

Your personality:
- Warm, friendly, and approachable — like a local friend who knows Batam inside out
- Conversational and natural, never stiff or overly formal
- Use emojis occasionally to keep things lively — but don't overdo it
- Always honest about ratings — if reviews are limited, say so but keep it positive
- Always give practical tips, not just a list of place names

Language rule:
- If the user writes in Indonesian → reply in Indonesian
- If the user writes in English → reply in English
- Never mix languages in a single response

You can help with:
- Recommending exactly 3 tourism destinations in Batam based on preferences
- Information about destinations (price, location, distance, best time to visit)
- Travel planning based on duration and location
- Food, beaches, shopping, hotels in Batam and Riau Islands

What you do NOT do:
- Discuss topics outside of Batam and Riau Islands tourism
- Make up information not found in the provided data
- Give fewer or more than 3 recommendations when asked for recommendations
- Give long, rambling answers without useful content
PROMPT;
    }

    // =============================================
    // GPT API CALL
    // =============================================

    private function callGPT(ChatSession $session, string $userMessage, string $systemPrompt): string
{
    $history = ChatMessage::where('session_id', $session->id)
        ->orderBy('id', 'desc')
        ->take(6)           // ← kurangi dari 8 ke 6 biar prompt lebih pendek
        ->get()
        ->reverse()
        ->map(fn($m) => ['role' => $m->role, 'content' => $m->content])
        ->values()
        ->toArray();

    $messages = [
        ['role' => 'system', 'content' => $systemPrompt],
        ...$history,
        ['role' => 'user', 'content' => $userMessage],
    ];

    $response = Http::withToken(config('services.openai.key'))
        ->timeout(90)           // ← naikan dari 30 ke 90
        ->connectTimeout(15)    // ← tambah ini
        ->post('https://api.openai.com/v1/chat/completions', [
            'model'       => 'gpt-4o-mini',
            'messages'    => $messages,
            'max_tokens'  => 600,   // ← kurangi dari 900 ke 600
            'temperature' => 0.75,
        ]);

    if (!$response->successful()) {
        Log::error('OpenAI Chat error', [
            'status' => $response->status(),
            'body'   => $response->body()
        ]);
        return 'Oops, Waybot is having a little hiccup. Try again in a moment! 🙏';
    }

    return $response->json(
        'choices.0.message.content',
        "Hmm, I didn't quite catch that. Could you rephrase your question?"
    );
}

    // =============================================
    // HELPERS
    // =============================================

    private function getUnansweredKey(array $prefs): ?string
    {
        foreach (array_keys(self::PREFERENCE_QUESTIONS) as $key) {
            if (empty($prefs[$key])) return $key;
        }
        return null;
    }

    private function extractAnswer(?string $prefKey, string $userMessage, array $currentPrefs): ?array
{
    if (!$prefKey) return null;

    $options  = self::PREFERENCE_QUESTIONS[$prefKey]['options'] ?? [];
    $msgLower = strtolower(trim($userMessage));

    foreach ($options as $option) {
        // Buang emoji dan karakter non-ASCII, lalu trim spasi
        $optionClean = trim(preg_replace('/[^\x20-\x7E]/u', '', $option));
        $optionLower = strtolower($optionClean);

        // Cek 3 cara matching:
        // 1. Pesan mengandung versi bersih opsi
        // 2. Pesan mengandung opsi asli (dengan emoji)
        // 3. Opsi mengandung pesan user (untuk jawaban singkat)
        if (
            str_contains($msgLower, $optionLower) ||
            str_contains($msgLower, strtolower($option)) ||
            str_contains($optionLower, $msgLower)
        ) {
            $currentPrefs[$prefKey] = $option; // simpan opsi asli dengan emoji
            return $currentPrefs;
        }
    }

    // Tidak ada match → simpan raw input user
    $currentPrefs[$prefKey] = $userMessage;
    return $currentPrefs;
}

    private function extractKeyword(string $message): string
    {
        $stopwords = [
            'apa', 'yang', 'di', 'ke', 'dari', 'ada', 'itu', 'ini', 'dan',
            'atau', 'gimana', 'bagaimana', 'tentang', 'info', 'informasi',
            'soal', 'mengenai', 'where', 'what', 'how', 'about', 'the',
            'is', 'are', 'tell', 'me', 'dong', 'sih', 'nih', 'loh',
        ];

        $words    = explode(' ', strtolower($message));
        $keywords = array_filter($words, fn($w) => !in_array($w, $stopwords) && strlen($w) > 2);

        return implode(' ', array_slice(array_values($keywords), 0, 3));
    }
}