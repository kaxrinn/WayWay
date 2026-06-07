@extends('layouts.travel-agent')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg text-white p-8 mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold">Welcome, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-blue-100 mt-2">Manage your tour packages and increase travel business visibility</p>
        </div>
        <i class="fas fa-suitcase-rolling text-8xl opacity-20"></i>
    </div>
</div>

<!-- Current Package Status - HIGHLIGHTED -->
<div class="mb-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-blue-500">
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-8 py-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Your Current Package Status</h2>
            <p class="text-gray-600 text-sm">Active subscription information</p>
        </div>

        @if($activeSubscription)
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Package Info -->
                <div>
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl text-white p-6 mb-6">
                        <p class="text-sm text-blue-100 uppercase font-semibold">Active Subscription Package</p>
                        <p class="text-4xl font-bold mt-2">{{ $activeSubscription->package->nama_paket }}</p>
                        
                        <div class="border-t border-blue-400 mt-4 pt-4">
                            <p class="text-blue-100 text-xs">Price</p>
                            @if($activeSubscription->package->harga == 0)
                                <p class="text-2xl font-bold text-green-300">FREE 🎉</p>
                            @else
                                <p class="text-2xl font-bold">Rp {{ number_format($activeSubscription->package->harga, 0, ',', '.') }}/bulan</p>
                            @endif
                        </div>

                        <div class="border-t border-blue-400 mt-4 pt-4">
                            <p class="text-blue-100 text-xs">Duration</p>
                            @if($activeSubscription->expired_at === null)
                                <p class="text-xl font-bold">Lifetime ♾️</p>
                            @else
                                <p class="text-sm font-semibold">
                                    Expires: {{ $activeSubscription->expired_at->format('d M Y') }}
                                </p>
                                <p class="text-xs text-blue-100 mt-1">
                                    ({{ $activeSubscription->expired_at->diffForHumans() }})
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                        <p class="text-yellow-800 text-sm">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Start:</strong> {{ $activeSubscription->started_at->format('d M Y H:i') }}
                        </p>
                    </div>
                </div>

                <!-- Quota Info -->
                <div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl text-white p-6 mb-6">
                        <p class="text-sm text-purple-100 uppercase font-semibold">Tour Package Quota</p>
                        
                        <div class="mt-6">
                            <div class="flex items-end justify-between mb-3">
                                <p class="text-3xl font-bold">{{ $currentPackages }}/{{ $maxPackages }}</p>
                                <p class="text-purple-100 text-xs">Used</p>
                            </div>

                            <!-- Progress Bar -->
                            <div class="bg-purple-400 rounded-full h-3 overflow-hidden">
                                <div class="bg-white h-full transition-all" style="width: {{ min(100, ($currentPackages / $maxPackages) * 100) }}%"></div>
                            </div>

                            <p class="text-purple-100 text-xs mt-3">
                                {{ $packagesAvailable }} {{ $packagesAvailable == 1 ? 'slot' : 'slots' }} remaining
                            </p>
                        </div>

                        <!-- Action -->
                        @if($packagesAvailable > 0)
                            <div class="border-t border-purple-400 mt-4 pt-4">
                                <a href="{{ route('travel-agent.packages.create') }}" 
                                   class="inline-block bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-purple-50 transition">
                                    <i class="fas fa-plus mr-2"></i> Upload Package
                                </a>
                            </div>
                        @else
                            <div class="border-t border-purple-400 mt-4 pt-4">
                                <p class="text-red-300 text-sm font-semibold">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    Quota full! Upgrade package to add slots.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Paket Features -->
                    @if($activeSubscription->package->fitur)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="font-bold text-gray-800 mb-3">Package Features</p>
                        <div class="space-y-2">
                            @foreach($activeSubscription->package->fitur as $fitur)
                            <div class="flex items-center text-gray-700 text-sm">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                {{ ucfirst(str_replace('_', ' ', $fitur)) }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Upgrade Button -->
            @if($activeSubscription->package->nama_paket !== 'Gold')
            <div class="border-t pt-6 mt-6">
                <a href="{{ route('travel-agent.subscriptions.upgrade') }}" 
                   class="inline-block bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-lg font-bold hover:shadow-lg transition">
                    <i class="fas fa-arrow-up mr-2"></i> Upgrade to a Better Package
                </a>
            </div>
            @endif
        </div>

        @else
        <!-- No Active Subscription -->
        <div class="p-12 text-center">
            <i class="fas fa-exclamation-triangle text-6xl text-red-400 mb-4"></i>
            <p class="text-xl font-bold text-gray-800">No Active Subscription</p>
            <p class="text-gray-600 mt-2">Contact admin for assistance</p>
            <a href="{{ route('travel-agent.contact-admin') }}" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg mt-4 hover:bg-blue-600 transition">
                <i class="fas fa-phone mr-2"></i> Contact Admin
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Paket -->
    <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Travel Packages</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['total_packages'] }}</p>
            </div>
            <i class="fas fa-box text-5xl text-blue-100"></i>
        </div>
    </div>

    <!-- Active Paket -->
    <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Active Packages</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['active_packages'] }}</p>
            </div>
            <i class="fas fa-star text-5xl text-green-100"></i>
        </div>
    </div>

    <!-- Slot Tersisa -->
    <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 {{ $packagesAvailable > 0 ? 'border-green-500' : 'border-orange-500' }}">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Slots Remaining</p>
                <p class="text-3xl font-bold {{ $packagesAvailable > 0 ? 'text-green-600' : 'text-orange-600' }}">{{ $packagesAvailable }}</p>
            </div>
            <i class="fas fa-boxes text-5xl {{ $packagesAvailable > 0 ? 'text-green-100' : 'text-orange-100' }}"></i>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    @if($packagesAvailable > 0)
    <a href="{{ route('travel-agent.packages.create') }}" 
       class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg text-white p-6 hover:shadow-xl transition">
        <i class="fas fa-plus-circle text-4xl mb-3"></i>
        <h3 class="text-lg font-bold mb-1">Upload New Package</h3>
        <p class="text-blue-100 text-sm">Add a new travel package ({{ $packagesAvailable }} slots remaining)</p>
    </a>
    @else
    <div class="bg-gradient-to-br from-gray-400 to-gray-500 rounded-xl shadow-lg text-white p-6 opacity-75 cursor-not-allowed">
        <i class="fas fa-lock text-4xl mb-3"></i>
        <h3 class="text-lg font-bold mb-1">Upload New Package</h3>
        <p class="text-gray-100 text-sm">Quota full. Upgrade package to open new slots</p>
    </div>
    @endif

    <a href="{{ route('travel-agent.packages.index') }}" 
       class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg text-white p-6 hover:shadow-xl transition">
        <i class="fas fa-list text-4xl mb-3"></i>
        <h3 class="text-lg font-bold mb-1">Manage Packages</h3>
        <p class="text-green-100 text-sm">Edit, delete, or view your packages</p>
    </a>

    <a href="{{ route('travel-agent.subscriptions.upgrade') }}" 
       class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg text-white p-6 hover:shadow-xl transition">
        <i class="fas fa-arrow-up text-4xl mb-3"></i>
        <h3 class="text-lg font-bold mb-1">Upgrade Package</h3>
        <p class="text-purple-100 text-sm">Raise your level for more benefits</p>
    </a>
</div>

<!-- Help Section -->
<div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
    <h3 class="font-bold text-blue-900 mb-2">Need Help?</h3>
    <p class="text-blue-800 text-sm mb-4">Our support team is ready to help you manage your travel business better.</p>
    <a href="{{ route('travel-agent.contact-admin') }}" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-600 transition">
        <i class="fas fa-phone mr-2"></i> Contact Admin
    </a>
</div>
@endsection