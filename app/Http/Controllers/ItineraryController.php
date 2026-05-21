<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\ItineraryHistory;
use App\Services\ItineraryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ItineraryController extends Controller
{
    public function __construct(private ItineraryService $itineraryService) {}

    // =============================================
    // GET /itinerary
    // Halaman utama planner (form + modal stepper)
    // =============================================
    public function index()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        $budgetOptions = [
            0      => 'Free',
            50000  => '50K',
            100000 => '100K',
            300000 => '300K',
        ];

        return view('wisatawan.itinerary.index', compact('kategoris', 'budgetOptions'));
    }

    // =============================================
    // POST /itinerary/generate
    // Generate itinerary, simpan ke DB, return JSON
    // Frontend akan redirect ke /itinerary/show/{id}
    // =============================================
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kategori_ids'      => 'required|array|min:1',
            'kategori_ids.*'    => 'integer|exists:kategori,id',
            'budget'            => 'required|numeric|min:0',
            'companion'         => 'required|in:solo,pasangan,keluarga,grup',
            'tanggal'           => 'required|date|after_or_equal:today',
            'origin_lat'        => 'nullable|numeric',
            'origin_lon'        => 'nullable|numeric',
            'origin_label'      => 'nullable|string|max:255',
            'max_destinations'  => 'nullable|integer|min:2|max:10',
            'available_hours'   => 'nullable|numeric|min:2|max:16',
        ]);

        try {
            $result = $this->itineraryService->generate($validated);

            if (isset($result['error'])) {
                return response()->json(['success' => false, 'message' => $result['error']], 422);
            }

            $history = ItineraryHistory::create([
                'user_id'           => Auth::id(),
                'params'            => $validated,
                'result'            => $result,
                'tanggal_kunjungan' => $validated['tanggal'],
                'companion'         => $validated['companion'],
                'origin_label'      => $validated['origin_label'] ?? 'Batam Center',
                'stop_count'        => $result['stop_count']      ?? 0,
                'total_distance'    => $result['total_distance']  ?? 0,
                'total_minutes'     => $result['total_minutes']   ?? 0,
                'budget'            => $validated['budget'],
            ]);

            // Kembalikan history_id → frontend redirect ke /itinerary/show/{id}
            return response()->json([
                'success' => true,
                'data'    => ['history_id' => $history->id],
            ]);

        } catch (\Exception $e) {
            \Log::error('Itinerary generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate itinerary. Please try again.',
            ], 500);
        }
    }

    // =============================================
    // GET /itinerary/show/{id}
    // ↳ show.blade.php — halaman hasil itinerary lengkap
    //   dengan peta Leaflet + schedule
    //   Dipanggil oleh:
    //   - Frontend setelah generate (redirect)
    //   - Tombol MATA di history.blade
    // =============================================
    public function show(int $id)
    {
        $history = ItineraryHistory::where('user_id', Auth::id())
            ->findOrFail($id);

        return view('wisatawan.itinerary.show', compact('history'));
    }

    // =============================================
    // GET /itinerary/history
    // ↳ history.blade.php — daftar semua itinerary user
    // =============================================
    public function history()
    {
        $histories = ItineraryHistory::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('wisatawan.itinerary.history', compact('histories'));
    }

    // =============================================
    // GET /itinerary/history/{id}
    // ↳ PDF stream di browser (preview tab baru)
    //   Tombol PREVIEW PDF di history & show page
    //   BUKAN untuk show.blade — ini langsung buka PDF
    // =============================================
    public function historyShow(int $id)
    {
        $history = ItineraryHistory::where('user_id', Auth::id())
            ->findOrFail($id);

        $pdf = Pdf::loadView('wisatawan.itinerary.pdf', [
            'history'   => $history,
            'itinerary' => $history->result,
            'params'    => $history->params,
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'          => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
        ]);

        return $pdf->stream('itinerary-' . $history->tanggal_kunjungan . '.pdf');
    }

    // =============================================
    // GET /itinerary/download/{id}
    // ↳ Force download PDF ke komputer user
    //   Tombol DOWNLOAD di history & show page
    // =============================================
    public function download(int $id)
    {
        $history = ItineraryHistory::where('user_id', Auth::id())
            ->findOrFail($id);

        $pdf = Pdf::loadView('wisatawan.itinerary.pdf', [
            'history'   => $history,
            'itinerary' => $history->result,
            'params'    => $history->params,
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'          => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
        ]);

        $filename = 'itinerary-' . $history->tanggal_kunjungan . '-' . $history->id . '.pdf';

        return $pdf->download($filename);
    }

    // =============================================
    // DELETE /itinerary/history/{id}
    // ↳ Hapus satu riwayat dari tabel history
    // =============================================
    public function historyDelete(int $id)
    {
        ItineraryHistory::where('user_id', Auth::id())
            ->findOrFail($id)
            ->delete();

        return back()->with('success', 'Itinerary deleted successfully.');
    }

    // ⚠️ METHOD view() DIHAPUS — tidak ada di route dan tidak dipakai
    // Route /{token} juga sudah dihapus dari web.php
}