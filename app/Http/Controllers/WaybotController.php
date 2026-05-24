<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Services\WaybotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WaybotController extends Controller
{
    public function __construct(private WaybotService $waybotService) {}

    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message'       => 'required|string|max:1000',
            'session_token' => 'nullable|string|max:64',
            'gps_lat'       => 'nullable|numeric',
            'gps_lng'       => 'nullable|numeric',
        ]);

        $session = $this->resolveSession($request);

        // Ambil GPS coords dari request kalau ada
        $gpsCoords = null;
        if ($request->filled('gps_lat') && $request->filled('gps_lng')) {
            $gpsCoords = [
                'lat' => (float) $request->gps_lat,
                'lng' => (float) $request->gps_lng,
            ];
        }

        try {
            $response = $this->waybotService->processMessage($session, $request->message, $gpsCoords);

            return response()->json([
                'success'         => true,
                'message'         => $response['message'],
                'type'            => $response['type'] ?? 'text',
                'options'         => $response['options'] ?? null,
                'pref_key'        => $response['pref_key'] ?? null,
                'has_gps'         => $response['has_gps'] ?? false,
                'destinasi_cards' => $response['destinasi_cards'] ?? null,
                'session_token'   => $session->session_token,
            ]);
        } catch (\Exception $e) {
    // Tambah log detail
    \Log::error('Waybot error', [
        'message' => $e->getMessage(),
        'file'    => $e->getFile(),
        'line'    => $e->getLine(),
    ]);
    
    return response()->json([
        'success' => false,
        'message' => 'Oops, Waybot is having a little hiccup. Try again in a moment! 🙏',
    ], 500);
}
    }

    public function reset(Request $request): JsonResponse
    {
        $token = $request->input('session_token');
        if ($token) ChatSession::where('session_token', $token)->delete();
        return response()->json(['success' => true]);
    }

    public function history(Request $request): JsonResponse
    {
        $token = $request->input('session_token');
        if (!$token) return response()->json(['messages' => []]);

        $session = ChatSession::where('session_token', $token)->first();
        if (!$session) return response()->json(['messages' => []]);

        $messages = $session->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => [
                'role'    => $m->role,
                'content' => $m->content,
                'time'    => $m->created_at->format('H:i'),
            ]);

        return response()->json(['messages' => $messages]);
    }

    private function resolveSession(Request $request): ChatSession
    {
        $token  = $request->input('session_token');
        $userId = auth()->id();

        $session = null;

        if ($token) {
            $session = ChatSession::where('session_token', $token)->first();
        } elseif ($userId) {
            $session = ChatSession::where('user_id', $userId)
                ->where('created_at', '>=', now()->subDay())
                ->latest()
                ->first();
        }

        if (!$session) {
            $session = ChatSession::create([
                'user_id'       => $userId,
                'session_token' => Str::random(40),
                'stage'         => 'greeting',
                'preferences'   => [],
            ]);
        }

        return $session;
    }
}