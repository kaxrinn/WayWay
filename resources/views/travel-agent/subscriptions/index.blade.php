@extends('layouts.travel-agent')

@section('title', 'My Active Packages')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-star text-yellow-500"></i>
                My Active Packages
            </h1>
            <p class="text-gray-500 mt-2">List of all your subscription packages</p>
        </div>
        <a href="{{ route('travel-agent.subscriptions.upgrade') }}" 
           class="bg-gradient-to-r from-primary to-blue-400 text-white px-6 py-3 rounded-lg font-bold hover:shadow-lg transition">
            <i class="fas fa-arrow-up mr-2"></i> Upgrade Package
        </a>
    </div>
</div>

<!-- Success Alert -->
@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow">
    <i class="fas fa-check-circle mr-2"></i>
    {{ session('success') }}
</div>
@endif

<!-- Subscriptions List -->
@if($subscriptions->count() > 0)
<div class="space-y-6">
    @foreach($subscriptions as $subscription)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
        <div class="bg-gradient-to-r from-primary to-blue-400 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-white">{{ $subscription->package->nama_paket }}</h3>
                    <p class="text-blue-100 text-sm">{{ $subscription->package->deskripsi }}</p>
                </div>
                <!-- Status Badge -->
                @if($subscription->status === 'active')
                    <span class="bg-green-500 text-white px-4 py-2 rounded-full font-bold text-sm">
                        <i class="fas fa-check-circle mr-1"></i> Active
                    </span>
                @elseif($subscription->status === 'pending')
                    <span class="bg-yellow-500 text-white px-4 py-2 rounded-full font-bold text-sm">
                        <i class="fas fa-clock mr-1"></i> Pending
                    </span>
                @else
                    <span class="bg-red-500 text-white px-4 py-2 rounded-full font-bold text-sm">
                        <i class="fas fa-times-circle mr-1"></i> Expired
                    </span>
                @endif
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <!-- Price -->
                <div class="border-l-4 border-blue-500 pl-4">
                    <p class="text-gray-600 text-sm font-semibold">Price</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">
                        @if($subscription->package->harga == 0)
                            <span class="text-green-600">FREE</span>
                        @else
                            Rp {{ number_format($subscription->package->harga, 0, ',', '.') }}
                        @endif
                    </p>
                </div>

                <!-- Max Packages -->
                <div class="border-l-4 border-green-500 pl-4">
                    <p class="text-gray-600 text-sm font-semibold">Max Packages</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $subscription->package->max_packages }}</p>
                </div>

                <!-- Start Date -->
                <div class="border-l-4 border-purple-500 pl-4">
                    <p class="text-gray-600 text-sm font-semibold">Start Date</p>
                    <p class="text-lg font-bold text-gray-800 mt-1">
                        @if($subscription->started_at)
                            {{ $subscription->started_at->format('d M Y') }}
                        @else
                            -
                        @endif
                    </p>
                </div>

                <!-- Expiry Date -->
                <div class="border-l-4 border-red-500 pl-4">
                    <p class="text-gray-600 text-sm font-semibold">Expiry Date</p>
                    <p class="text-lg font-bold mt-1">
                        @if($subscription->expired_at)
                            <span class="text-gray-800">{{ $subscription->expired_at->format('d M Y') }}</span>
                        @else
                            <span class="text-green-600">♾️ Lifetime</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Features -->
            @if($subscription->package->fitur && count($subscription->package->fitur) > 0)
            <div class="border-t pt-4">
                <p class="font-semibold text-gray-800 mb-3">Package Features:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($subscription->package->fitur as $fitur)
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                        <i class="fas fa-check mr-1"></i>{{ ucfirst(str_replace('_', ' ', $fitur)) }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Actions -->
            @if($subscription->status === 'pending')
            <div class="border-t mt-4 pt-4 text-center">
                <p class="text-gray-600 text-sm mb-3">
                    <i class="fas fa-info-circle mr-2"></i>
                    Waiting for payment verification...
                </p>
                <p class="text-gray-500 text-xs">Package will be automatically activated after successful payment verification</p>
            </div>
            @endif

            @if($subscription->status === 'expired')
            <div class="border-t mt-4 pt-4 text-center">
                <p class="text-red-600 text-sm font-semibold mb-3">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    This package has expired
                </p>
                <a href="{{ route('travel-agent.subscriptions.upgrade') }}" 
                   class="inline-block bg-gradient-to-r from-primary to-blue-400 text-white px-4 py-2 rounded-lg font-bold hover:shadow-lg transition text-sm">
                    <i class="fas fa-refresh mr-1"></i> Renew or Upgrade
                </a>
            </div>
            @endif
        </div>
    </div>
    @endforeach

    <!-- Pagination -->
    @if($subscriptions->hasPages())
    <div class="flex justify-center mt-8">
        {{ $subscriptions->links() }}
    </div>
    @endif
</div>

@else
<!-- Empty State -->
<div class="bg-white rounded-xl shadow-lg p-12 text-center">
    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
    <h3 class="text-2xl font-bold text-gray-800 mb-2">No Active Packages Yet</h3>
    <p class="text-gray-600 mb-6">You don't have any subscription packages. Choose and activate one now!</p>
    <a href="{{ route('travel-agent.subscriptions.upgrade') }}" 
       class="inline-block bg-gradient-to-r from-primary to-blue-400 text-white px-8 py-3 rounded-lg font-bold hover:shadow-lg transition">
        <i class="fas fa-plus mr-2"></i> Choose a Package
    </a>
</div>
@endif

<!-- Info Box -->
<div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 mt-8">
    <h4 class="font-bold text-blue-900 mb-3">
        <i class="fas fa-lightbulb mr-2"></i>
        Package Information
    </h4>
    <ul class="text-blue-800 space-y-2 text-sm">
        <li>✅ <strong>Free Package (Basic):</strong> 1 tour package, lifetime</li>
        <li>✅ <strong>Silver:</strong> 5 tour packages, valid for 1 month</li>
        <li>✅ <strong>Gold:</strong> 15 tour packages, valid for 1 month</li>
        <li>✅ Package will be automatically active after successful Midtrans payment</li>
        <li>✅ Contact admin if you have any questions about the packages</li>
    </ul>
</div>
@endsection