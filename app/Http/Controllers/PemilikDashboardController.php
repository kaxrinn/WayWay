<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use App\Models\EditRequest;
use Illuminate\Http\Request;

class PemilikDashboardController extends Controller
{
    /**
     * Dashboard pemilik wisata
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get paket info - assign Basic if null
        $currentPaket = $user->currentPaket;
        
        if (!$currentPaket) {
            // Auto-assign Basic paket
            $basicPaket = \App\Models\PaketPromosi::where('nama_paket', 'Basic')->first();
            if ($basicPaket) {
                $user->update(['current_paket_id' => $basicPaket->id]);
                $currentPaket = $basicPaket;
            }
        }
        
        // Get limits (dengan fallback jika null)
        $limits = $currentPaket ? [
            'max_destinasi' => $currentPaket->max_destinasi,
            'max_foto' => $currentPaket->max_foto,
            'max_video' => $currentPaket->max_video,
            'can_edit_foto' => $currentPaket->can_edit_foto,
            'is_featured_allowed' => $currentPaket->is_featured_allowed,
        ] : [
            'max_destinasi' => 1,
            'max_foto' => 3,
            'max_video' => 0,
            'can_edit_foto' => false,
            'is_featured_allowed' => false,
        ];
        
        // Count destinasi aktif
        $jumlahDestinasi = $user->destinasi()->where('status', 'active')->count();
        
        // Count total foto & video dari semua destinasi
        $totalFoto = 0;
        $totalVideo = 0;
        
        foreach ($user->destinasi as $destinasi) {
            $totalFoto += count($destinasi->foto ?? []);
            $totalVideo += count($destinasi->video ?? []);
        }
        
        // Get pending edit requests (untuk Basic users)
        $pendingRequests = \App\Models\EditRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        
        // Recent destinasi
        $recentDestinasi = $user->destinasi()
            ->with('kategori')
            ->latest()
            ->take(5)
            ->get();
        
        // Check if paket expired
        $isPaketExpired = $currentPaket && $user->paket_expired_at && $user->paket_expired_at < now();
        
        return view('pemilik.dashboard', compact(
            'currentPaket',
            'limits',
            'jumlahDestinasi',
            'totalFoto',
            'totalVideo',
            'pendingRequests',
            'recentDestinasi',
            'isPaketExpired'
        ));
    }
}