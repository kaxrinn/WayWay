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

    /**
     * Kirim pesan ke Waybot dan dapatkan respons.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message'       => 'required|string|max:1000',
            'session_token' => 'nullable|string|max:64',
        ]);

        // Ambil atau buat sesi chat
        $session = $this->resolveSession($request);

        try {
            $response = $this->waybotService->processMessage($session, $request->message);

            return response()->json([
                'success'       => true,
                'message'       => $response['message'],
                'type'          => $response['type'] ?? 'text',
                'options'       => $response['options'] ?? null,
                'pref_key'      => $response['pref_key'] ?? null,
                'destinasi_cards'=> $response['destinasi_cards'] ?? null,
                'session_token' => $session->session_token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Waybot lagi sibuk, coba sebentar lagi ya! 🙏',
            ], 500);
        }
    }

    /**
     * Reset sesi chat (mulai dari awal).
     */
    public function reset(Request $request): JsonResponse
    {
        $token = $request->input('session_token');

        if ($token) {
            ChatSession::where('session_token', $token)->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Ambil riwayat chat untuk sesi tertentu.
     */
    public function history(Request $request): JsonResponse
    {
        $token = $request->input('session_token');

        if (! $token) {
            return response()->json(['messages' => []]);
        }

        $session = ChatSession::where('session_token', $token)->first();

        if (! $session) {
            return response()->json(['messages' => []]);
        }

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

    /**
     * Resolve atau buat session chat.
     */
    private function resolveSession(Request $request): ChatSession
    {
        $token   = $request->input('session_token');
        $userId  = auth()->id();

        // Cari session yang sudah ada
        $session = null;

        if ($token) {
            $session = ChatSession::where('session_token', $token)->first();
        } elseif ($userId) {
            // User yang login, cari sesi aktif (dibuat dalam 24 jam terakhir)
            $session = ChatSession::where('user_id', $userId)
                ->where('created_at', '>=', now()->subDay())
                ->latest()
                ->first();
        }

        if (! $session) {
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