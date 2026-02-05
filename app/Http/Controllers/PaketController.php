<?php

namespace App\Http\Controllers;

use App\Models\PaketPromosi;
use App\Models\TransaksiPromosi;
use App\Models\Promosi;
use Illuminate\Http\Request;

class PaketController extends Controller
{
    /**
     * Display list paket promosi
     */
    public function index()
    {
        $pakets = PaketPromosi::where('status', 'active')
            ->orderBy('priority_level')
            ->get();
        
        $currentPaket = auth()->user()->currentPaket;
        
        // Auto-assign Basic if null
        if (!$currentPaket) {
            $basicPaket = PaketPromosi::where('nama_paket', 'Basic')->first();
            if ($basicPaket) {
                auth()->user()->update(['current_paket_id' => $basicPaket->id]);
                $currentPaket = $basicPaket;
            }
        }
        
        $isPaketExpired = $currentPaket && auth()->user()->paket_expired_at && auth()->user()->paket_expired_at < now();
        
        return view('pemilik.paket.index', compact('pakets', 'currentPaket', 'isPaketExpired'));
    }

    /**
     * Checkout paket (will redirect to Midtrans)
     */
    public function checkout(Request $request, $paketId)
    {
        $user = auth()->user();
        $paket = PaketPromosi::findOrFail($paketId);
        
        // Validasi: tidak bisa checkout paket yang sama
        if ($user->current_paket_id == $paketId && $user->isPaketActive()) {
            return back()->with('error', 'Anda sudah menggunakan paket ini!');
        }
        
        // Validasi: Basic tidak bisa di-checkout (gratis)
        if ($paket->harga == 0) {
            return back()->with('error', 'Paket Basic gratis, tidak perlu checkout!');
        }
        
        $validated = $request->validate([
            'durasi' => 'required|in:monthly,yearly',
        ]);
        
        // Calculate price
        $durasi = $validated['durasi'];
        $totalHarga = $durasi == 'monthly' ? $paket->harga : ($paket->harga * 12 * 0.9); // 10% discount yearly
        $durasiHari = $durasi == 'monthly' ? 30 : 365;
        
        // Create promosi record
        $promosi = Promosi::create([
            'user_id' => $user->id, // ADD user_id untuk user subscription
            'destinasi_id' => null, // NULL untuk user subscription (bukan destinasi spesifik)
            'paket_id' => $paket->id,
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays($durasiHari),
            'status' => 'pending',
        ]);
        
        // Create transaksi
        $transaksi = TransaksiPromosi::create([
            'promosi_id' => $promosi->id,
            'user_id' => $user->id,
            'paket_id' => $paket->id,
            'total_harga' => $totalHarga,
            'metode_pembayaran' => 'midtrans',
            'status_pembayaran' => 'pending',
            'tanggal_transaksi' => now(),
        ]);
        
        // TODO: Integrate with Midtrans
        // For now, just redirect to success page with manual confirmation
        
        return view('pemilik.paket.checkout', compact('transaksi', 'paket', 'durasi', 'totalHarga'));
    }

    /**
     * Manual confirmation (sementara sebelum Midtrans)
     */
    public function confirmPayment(Request $request, $transaksiId)
    {
        $transaksi = TransaksiPromosi::where('user_id', auth()->id())->findOrFail($transaksiId);
        
        $validated = $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Upload bukti
        $path = $request->file('bukti_pembayaran')->store('transaksi/bukti', 'public');
        
        $transaksi->update([
            'bukti_pembayaran' => $path,
            'status_pembayaran' => 'pending', // Admin yang approve
        ]);
        
        return redirect()->route('pemilik.paket.index')
            ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu konfirmasi admin.');
    }
}