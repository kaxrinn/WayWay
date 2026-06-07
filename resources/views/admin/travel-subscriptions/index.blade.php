@extends('layouts.admin')

@section('title', 'Travel Agent Subscriptions')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
        <i class="fas fa-receipt text-green-500"></i>
        Travel Agent Subscriptions
    </h1>
    <p class="text-gray-500 mt-2">Monitor all package subscription purchases from travel agents (Auto-Active when payment is successful)</p>
</div>

<!-- Success Alert -->
@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow">
    <i class="fas fa-check-circle mr-2"></i>
    {{ session('success') }}
</div>
@endif

<!-- Info Alert -->
<div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 mb-6">
    <p class="text-blue-900">
        <i class="fas fa-info-circle mr-2"></i>
        <strong>Note:</strong> Package will automatically activate when payment is successful in Midtrans. No manual approval needed.
    </p>
</div>

<!-- Subscriptions Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-green-500 to-emerald-500 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">No</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Travel Agent</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Package</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Price</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Payment Method</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Period</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($subscriptions as $key => $subscription)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-semibold text-gray-600">
                        {{ ($subscriptions->currentPage() - 1) * $subscriptions->perPage() + $key + 1 }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-800">{{ $subscription->travelAgent->name }}</div>
                        <div class="text-xs text-gray-500">{{ $subscription->travelAgent->email }}</div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $subscription->package->nama_paket }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-primary">
                        @if($subscription->package->harga == 0)
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">
                                <i class="fas fa-gift mr-1"></i> FREE
                            </span>
                        @else
                            Rp {{ number_format($subscription->package->harga, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($subscription->payment_method === 'free')
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-bold">
                                <i class="fas fa-check mr-1"></i> Free Package
                            </span>
                        @else
                            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-bold">
                                <i class="fas fa-credit-card mr-1"></i> Midtrans
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($subscription->status === 'active')
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                <i class="fas fa-check-circle"></i> Active
                            </span>
                        @elseif($subscription->status === 'pending')
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                        @else
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                <i class="fas fa-times-circle"></i> Expired
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="text-gray-700">
                            @if($subscription->started_at)
                                <strong>Start:</strong> {{ $subscription->started_at->format('d M Y') }}<br>
                            @endif
                            @if($subscription->expired_at)
                                <strong>End:</strong> {{ $subscription->expired_at->format('d M Y') }}
                            @else
                                <span class="text-green-600 font-semibold">
                                    <i class="fas fa-infinity"></i> Lifetime
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex gap-2 justify-center flex-wrap">
                            <a href="{{ route('admin.travel-subscriptions.show', $subscription->id) }}" 
                               class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-semibold">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                            
                            @if($subscription->status === 'pending')
                            <form method="POST" action="{{ route('admin.travel-subscriptions.approve', $subscription->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs font-semibold" 
                                        onclick="return confirm('Approve subscription ini?')">
                                    <i class="fas fa-check mr-1"></i> Approve
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('admin.travel-subscriptions.reject', $subscription->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold"
                                        onclick="return confirm('Reject subscription ini?')">
                                    <i class="fas fa-ban mr-1"></i> Reject
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-inbox text-6xl mb-4"></i>
                        <p class="text-lg font-medium">No subscriptions found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t bg-gray-50">
        {{ $subscriptions->links() }}
    </div>
</div>

<!-- Info Card -->
<div class="bg-green-50 border-2 border-green-200 rounded-xl p-6 mt-6">
    <h3 class="text-lg font-bold text-green-900 mb-3">
        <i class="fas fa-lightbulb mr-2"></i>
        How Subscription works?
    </h3>
    <ul class="text-green-800 space-y-2">
        <li>✅ Travel Agent buy a package → status "Pending"</li>
        <li>✅ Travel Agent payment in Midtrans → Webhook triggered</li>
        <li>✅ Payment successful → Status automatically "Active"</li>
        <li>✅ Package can use automatically!</li>
        <li>❌ Payment failed/cancelled → Status "Expired"</li>
    </ul>
</div>
@endsection