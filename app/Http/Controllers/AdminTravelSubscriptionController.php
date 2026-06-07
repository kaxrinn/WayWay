<?php

namespace App\Http\Controllers;

use App\Models\TravelAgentSubscription;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTravelSubscriptionController extends Controller
{
    /**
     * List semua subscriptions/transactions
     */
    public function index()
    {
        $subscriptions = TravelAgentSubscription::with(['travelAgent', 'package'])
            ->latest()
            ->paginate(15);

        return view('admin.travel-subscriptions.index', compact('subscriptions'));
    }

    /**
     * Detail subscription
     */
    public function show($id)
    {
        $subscription = TravelAgentSubscription::with(['travelAgent', 'package'])->findOrFail($id);
        return view('admin.travel-subscriptions.show', compact('subscription'));
    }

    /**
     * Approve subscription (jika pending)
     */
    public function approve($id)
{
    $subscription = TravelAgentSubscription::with('package')
        ->findOrFail($id);

    TravelAgentSubscription::where(
        'travel_agent_id',
        $subscription->travel_agent_id
    )
    ->where('id', '!=', $subscription->id)
    ->where('status', 'active')
    ->whereHas('package', function ($query) {
        $query->where('harga', '>', 0);
    })
    ->update([
        'status' => 'expired'
    ]);

    $subscription->update([
        'status' => 'active',
        'started_at' => now(),
        'expired_at' => $subscription->package->durasi_bulan > 0
            ? now()->addMonths($subscription->package->durasi_bulan)
            : null,
    ]);

    return back()->with('success', 'Subscription berhasil diaktifkan!');
}

    /**
     * Reject/cancel subscription
     */
    public function reject($id)
    {
        $subscription = TravelAgentSubscription::findOrFail($id);
        $subscription->update(['status' => 'expired']);

        return back()->with('success', 'Subscription dibatalkan!');
    }
}