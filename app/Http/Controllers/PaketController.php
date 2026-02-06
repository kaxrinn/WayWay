<?php

namespace App\Http\Controllers;

use App\Models\PaketPromosi;
use App\Models\TransaksiPromosi;
use App\Models\Promosi;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PaketController extends Controller
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }
    
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
     * Checkout paket (UPDATED: Midtrans integration)
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
            'user_id' => $user->id,
            'destinasi_id' => null,
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
        
        // ✅ MIDTRANS INTEGRATION - CREATE SNAP TOKEN
        try {
            $params = [
                'transaction_details' => [
                    'order_id' => 'TRX-' . $transaksi->id . '-' . time(),
                    'gross_amount' => (int) $totalHarga,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->no_telepon ?? '08123456789',
                ],
                'item_details' => [[
                    'id' => $paket->id,
                    'price' => (int) $totalHarga,
                    'quantity' => 1,
                    'name' => 'Paket ' . $paket->nama_paket . ' - ' . ucfirst($durasi)
                ]],
            ];
            
            $snapToken = Snap::getSnapToken($params);
            
            // Save snap token
            $transaksi->update(['snap_token' => $snapToken]);
            
            return view('pemilik.paket.checkout', [
                'transaksi' => $transaksi,
                'paket' => $paket,
                'durasi' => $durasi,
                'totalHarga' => $totalHarga,
                'snapToken' => $snapToken,
                'clientKey' => config('midtrans.client_key')
            ]);
            
        } catch (\Exception $e) {
            // Rollback if Midtrans fails
            $transaksi->delete();
            $promosi->delete();
            
            return back()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Callback after payment (success/pending/failed)
     */
    public function callback(Request $request)
    {
        return redirect()->route('pemilik.paket.index')
            ->with('success', 'Pembayaran sedang diproses. Silakan tunggu konfirmasi.');
    }
    
    /**
     * Midtrans notification webhook
     */
    public function notification(Request $request)
    {
        $json = json_decode($request->getContent());
        
        $orderId = $json->order_id ?? null;
        $statusCode = $json->status_code ?? null;
        $grossAmount = $json->gross_amount ?? null;
        $signatureKey = $json->signature_key ?? null;
        
        // Extract transaction ID from order_id (format: TRX-{id}-{timestamp})
        $parts = explode('-', $orderId);
        $transaksiId = $parts[1] ?? null;
        
        if (!$transaksiId) {
            return response()->json(['message' => 'Invalid order ID'], 400);
        }
        
        $transaksi = TransaksiPromosi::find($transaksiId);
        
        if (!$transaksi) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
        
        // Verify signature
        $serverKey = config('midtrans.server_key');
        $hash = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        
        if ($hash !== $signatureKey) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }
        
        // Update transaction based on status
        $transactionStatus = $json->transaction_status ?? null;
        
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            // Payment SUCCESS
            $transaksi->update(['status_pembayaran' => 'success']);
            
            if ($transaksi->promosi) {
                $transaksi->promosi->update(['status' => 'active']);
                
                if ($transaksi->user) {
                    $transaksi->user->update([
                        'current_paket_id' => $transaksi->paket_id,
                        'paket_expired_at' => $transaksi->promosi->tanggal_selesai,
                    ]);
                }
            }
        } 
        elseif ($transactionStatus == 'pending') {
            $transaksi->update(['status_pembayaran' => 'pending']);
        }
        elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            // Payment FAILED
            $transaksi->update(['status_pembayaran' => 'failed']);
            
            if ($transaksi->promosi) {
                $transaksi->promosi->update(['status' => 'expired']);
            }
        }
        
        return response()->json(['message' => 'OK']);
    }

     public function destroy($id)
    {
        TransaksiPromosi::findOrFail($id)->delete();
        return back()->with('success','Transaksi berhasil dihapus.');
    }
    /**
     * Manual confirmation (TETAP ADA - untuk backup jika Midtrans error)
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