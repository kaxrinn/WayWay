<?php

namespace App\Http\Controllers;

use App\Models\TravelAgentSubscriptionPackage;
use App\Models\TravelAgentSubscription;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TravelAgentSubscriptionController extends Controller
{
    public function __construct()
    {
        // Setup Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * List semua subscription travel agent
     */
    public function index()
    {
        $userId = auth()->id();

        $subscriptions = TravelAgentSubscription::where('travel_agent_id', $userId)
            ->with('package')
            ->latest('created_at')
            ->paginate(10);  // ← Changed from get() to paginate()

        return view('travel-agent.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show halaman upgrade paket
     */
    public function upgrade()
    {
        $userId = auth()->id();

        // Semua paket yang tersedia
        $packages = TravelAgentSubscriptionPackage::where('status', 'active')
            ->orderBy('harga')
            ->get();

        // Paket yang sudah dibeli user (aktif)
        $currentSubscriptions = TravelAgentSubscription::where('travel_agent_id', $userId)
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expired_at')
                  ->orWhere('expired_at', '>', now());
            })
            ->pluck('package_id')
            ->toArray();

        return view('travel-agent.subscriptions.upgrade', compact('packages', 'currentSubscriptions'));
    }

    /**
     * Proses checkout
     */
    public function checkout($packageId)
    {
        $userId = auth()->id();
        $user = auth()->user();

        // Validate user email
        if (!$user->email || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('travel-agent.profile')
                ->with('error', 'Email Anda tidak valid. Silakan update profile terlebih dahulu sebelum membeli paket.');
        }

        // Get package
        $package = TravelAgentSubscriptionPackage::findOrFail($packageId);

        // Jika paket gratis, langsung aktifkan
        if ($package->harga == 0) {
            // Cek apakah sudah ada subscription yang active
            $existing = TravelAgentSubscription::where('travel_agent_id', $userId)
                ->where('package_id', $packageId)
                ->where('status', 'active')
                ->where(function($q) {
                    $q->whereNull('expired_at')->orWhere('expired_at', '>', now());
                })
                ->first();

            if ($existing) {
                return redirect()->route('travel-agent.subscriptions.upgrade')
                    ->with('error', 'Anda sudah memiliki paket ini dan masih aktif.');
            }

            // Hapus subscription yang sudah expired atau pending
            TravelAgentSubscription::where('travel_agent_id', $userId)
                ->where('package_id', $packageId)
                ->whereIn('status', ['expired', 'pending'])
                ->delete();

            // Create new active subscription
            $subscription = TravelAgentSubscription::create([
                'travel_agent_id' => $userId,
                'package_id' => $packageId,
                'payment_method' => 'free',
                'status' => 'active',
                'started_at' => now(),
                'expired_at' => ($package->durasi_bulan == 0) ? null : now()->addMonths($package->durasi_bulan),
            ]);

            return redirect()->route('travel-agent.subscriptions.index')
                ->with('success', 'Paket gratis berhasil diaktifkan!');
        }

        // Untuk paket berbayar
        // Cek apakah sudah ada subscription yang pending atau active
        $existing = TravelAgentSubscription::where('travel_agent_id', $userId)
            ->where('package_id', $packageId)
            ->whereIn('status', ['pending', 'active'])
            ->first();

        if ($existing) {
            return redirect()->route('travel-agent.subscriptions.upgrade')
                ->with('error', 'Anda sudah memiliki paket ini atau sedang dalam proses pembayaran.');
        }

        // Jika ada subscription dengan status expired, hapus dulu
        TravelAgentSubscription::where('travel_agent_id', $userId)
            ->where('package_id', $packageId)
            ->where('status', 'expired')
            ->delete();

        // Create pending subscription
        $subscription = TravelAgentSubscription::create([
            'travel_agent_id' => $userId,
            'package_id' => $packageId,
            'payment_method' => 'midtrans',
            'status' => 'pending',
        ]);

        // Generate Snap token
        $orderId = 'TRAVEL-AGENT-' . $userId . '-' . $packageId . '-' . $subscription->id;

        $transactionDetails = [
            'order_id' => $orderId,
            'gross_amount' => (int) $package->harga,
        ];

        $customerDetails = [
            'first_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->no_telepon ?? '0',
        ];

        $items = [
            [
                'id' => 'paket-' . $packageId,
                'price' => (int) $package->harga,
                'quantity' => 1,
                'name' => $package->nama_paket,
            ]
        ];

        $payload = [
            'transaction_details' => $transactionDetails,
            'customer_details' => $customerDetails,
            'items' => $items,
            'callbacks' => [
                'finish' => route('travel-agent.subscriptions.callback'),
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($payload);
            $subscription->snap_token = $snapToken;
            $subscription->save();

            // Log untuk debug
            Log::info('Snap token created', [
                'order_id' => $orderId,
                'subscription_id' => $subscription->id,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans error: ' . $e->getMessage());
            return redirect()->route('travel-agent.subscriptions.upgrade')
                ->with('error', 'Gagal membuat token pembayaran: ' . $e->getMessage());
        }

        return view('travel-agent.subscriptions.checkout', [
            'package' => $package,
            'subscription' => $subscription,
            'snapToken' => $snapToken,
            'clientKey' => config('midtrans.client_key'),
        ]);
    }

    /**
     * Callback setelah pembayaran
     */
    public function callback()
    {
        return redirect()->route('travel-agent.subscriptions.index')
            ->with('success', 'Proses checkout selesai. Status pembayaran sedang diverifikasi.');
    }

    /**
     * WEBHOOK HANDLER - AUTO ACTIVATE ketika payment success
     * 
     * Midtrans akan POST ke endpoint ini dengan status pembayaran
     */
    public function notification(Request $request)
    {
        $orderId = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status');
        $paymentType = $request->input('payment_type');

        // Log incoming webhook
        Log::info('Midtrans Webhook Received', [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'payment_type' => $paymentType,
            'all_data' => $request->all(),
        ]);

        // Parse order ID: TRAVEL-AGENT-{userId}-{packageId}-{subscriptionId}
        $parts = explode('-', $orderId);
        
        if (count($parts) < 5 || $parts[0] !== 'TRAVEL' || $parts[1] !== 'AGENT') {
            Log::warning('Invalid order_id format', ['order_id' => $orderId]);
            return response()->json(['status' => 'ignored'], 200);
        }

        $subscriptionId = (int) end($parts);

        // Find subscription
        $subscription = TravelAgentSubscription::with('package')->find($subscriptionId);

        if (!$subscription) {
            Log::warning('Subscription not found', ['subscription_id' => $subscriptionId]);
            return response()->json(['status' => 'subscription_not_found'], 404);
        }

        // Update subscription status based on payment status
        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            // ✅ PEMBAYARAN BERHASIL - AUTO ACTIVATE!
            $durasi = $subscription->package->durasi_bulan;
            $expiredAt = ($durasi == 0) ? null : now()->addMonths($durasi);

            $subscription->update([
                'status' => 'active',        // ← AUTO ACTIVE!
                'started_at' => now(),
                'expired_at' => $expiredAt,
            ]);

            Log::info('Subscription activated', [
                'subscription_id' => $subscriptionId,
                'package' => $subscription->package->nama_paket,
            ]);

        } elseif (in_array($transactionStatus, ['pending'])) {
            // MASIH PENDING - tunggu konfirmasi
            Log::info('Payment pending', ['subscription_id' => $subscriptionId]);

        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            // ❌ PEMBAYARAN GAGAL
            $subscription->update(['status' => 'expired']);

            Log::info('Payment failed/cancelled', [
                'subscription_id' => $subscriptionId,
                'reason' => $transactionStatus,
            ]);
        }

        // Return success untuk Midtrans
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * ADMIN: Approve subscription (manual approval)
     * Ubah status pending → active
     */
    public function approve($subscriptionId)
    {
        $subscription = TravelAgentSubscription::findOrFail($subscriptionId);

        // Only approve pending subscriptions
        if ($subscription->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Hanya subscription dengan status "pending" yang bisa di-approve.');
        }

        // Update to active
        $durasi = $subscription->package->durasi_bulan;
        $subscription->update([
            'status' => 'active',
            'started_at' => now(),
            'expired_at' => ($durasi == 0) ? null : now()->addMonths($durasi),
        ]);

        Log::info('Subscription approved by admin', [
            'subscription_id' => $subscriptionId,
            'travel_agent' => $subscription->travelAgent->name,
            'package' => $subscription->package->nama_paket,
        ]);

        return redirect()->back()
            ->with('success', 'Subscription ' . $subscription->travelAgent->name . ' berhasil di-approve!');
    }

    /**
     * ADMIN: Reject subscription (manual rejection)
     * Ubah status pending → expired
     */
    public function reject($subscriptionId)
    {
        $subscription = TravelAgentSubscription::findOrFail($subscriptionId);

        // Only reject pending subscriptions
        if ($subscription->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Hanya subscription dengan status "pending" yang bisa di-reject.');
        }

        // Update to expired
        $subscription->update([
            'status' => 'expired',
        ]);

        Log::info('Subscription rejected by admin', [
            'subscription_id' => $subscriptionId,
            'travel_agent' => $subscription->travelAgent->name,
            'package' => $subscription->package->nama_paket,
        ]);

        return redirect()->back()
            ->with('success', 'Subscription ' . $subscription->travelAgent->name . ' berhasil di-reject!');
    }
}