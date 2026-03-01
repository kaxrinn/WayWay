@extends('layouts.pemilik')

@section('title', 'Tourism Owner Dashboard')

@section('content')
<!-- Welcome Header -->
<div class="bg-gradient-to-r from-primary to-blue-400 rounded-xl shadow-lg p-6 sm:p-8 mb-6 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">Welcome, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-white/90 text-sm sm:text-base">Manage your tourism destinations with ease</p>
        </div>
        <div class="hidden md:block">
            <i class="fas fa-map-marked-alt text-8xl opacity-20"></i>
        </div>
    </div>
</div>

<!-- Package Expired Warning -->
@if($isPaketExpired)
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
    <div class="flex items-start sm:items-center gap-3">
        <i class="fas fa-exclamation-triangle text-xl sm:text-2xl flex-shrink-0 mt-0.5 sm:mt-0"></i>
        <div>
            <p class="font-bold text-sm sm:text-base">Your Package Has Expired!</p>
            <p class="text-sm">Your package expired on {{ auth()->user()->paket_expired_at->format('d M Y') }}.
                <a href="{{ route('pemilik.paket.index') }}" class="underline font-semibold">Upgrade now</a> to continue.</p>
        </div>
    </div>
</div>
@endif

<!-- Active Package Card -->
<div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg sm:text-xl font-bold text-gray-800">📦 Active Package</h2>
        <a href="{{ route('pemilik.paket.index') }}"
           class="text-primary hover:text-blue-600 font-semibold text-sm">
            Upgrade <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    @if($currentPaket)
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-3 sm:p-4 rounded-lg">
            <p class="text-xs sm:text-sm text-gray-600 mb-1">Package</p>
            <p class="text-xl sm:text-2xl font-bold text-blue-600">{{ $currentPaket->nama_paket }}</p>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 p-3 sm:p-4 rounded-lg">
            <p class="text-xs sm:text-sm text-gray-600 mb-1">Expires</p>
            <p class="text-base sm:text-lg font-bold text-green-600">
                {{ auth()->user()->paket_expired_at ? auth()->user()->paket_expired_at->format('d M Y') : 'Forever' }}
            </p>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-3 sm:p-4 rounded-lg">
            <p class="text-xs sm:text-sm text-gray-600 mb-1">Max Destinations</p>
            <p class="text-xl sm:text-2xl font-bold text-purple-600">
                {{ $limits['max_destinasi'] ?? '∞' }}
            </p>
        </div>

        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-3 sm:p-4 rounded-lg">
            <p class="text-xs sm:text-sm text-gray-600 mb-1">Max Photos/Videos</p>
            <p class="text-xl sm:text-2xl font-bold text-yellow-600">
                {{ $limits['max_foto'] ?? '∞' }} / {{ $limits['max_video'] ?? '∞' }}
            </p>
        </div>
    </div>
    @else
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <p class="text-red-700 font-semibold text-sm sm:text-base">No package set! Contact admin for activation.</p>
    </div>
    @endif
</div>

<!-- Stats Cards -->
@php
    $isPremium = isset($currentPaket) && $currentPaket && $currentPaket->priority_level >= 3;
@endphp

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="bg-blue-100 p-3 sm:p-4 rounded-full">
                <i class="fas fa-map-marked-alt text-xl sm:text-2xl text-blue-500"></i>
            </div>
        </div>
        <p class="text-gray-500 text-xs sm:text-sm mb-1">Active Destinations</p>
        <div class="flex items-baseline gap-2">
            <h3 class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $jumlahDestinasi }}</h3>
            <span class="text-xs sm:text-sm text-gray-500">/ {{ $limits['max_destinasi'] ?? '∞' }}</span>
        </div>
        @if($limits['max_destinasi'] && $jumlahDestinasi >= $limits['max_destinasi'])
            <p class="text-xs text-red-500 mt-2">Limit reached!</p>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="bg-green-100 p-3 sm:p-4 rounded-full">
                <i class="fas fa-images text-xl sm:text-2xl text-green-500"></i>
            </div>
        </div>
        <p class="text-gray-500 text-xs sm:text-sm mb-1">Total Photos</p>
        <div class="flex items-baseline gap-2">
            <h3 class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalFoto }}</h3>
        </div>
    </div>

    <!-- Stat: Reviews -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="bg-yellow-100 p-3 sm:p-4 rounded-full">
                <i class="fas fa-star text-xl sm:text-2xl text-yellow-500"></i>
            </div>
        </div>
        <p class="text-gray-500 text-xs sm:text-sm mb-1">Total Reviews</p>
        <div class="flex items-baseline gap-2">
            <h3 class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalUlasan ?? 0 }}</h3>
        </div>
        <p class="text-xs text-gray-400 mt-2">
            Avg
            <span class="text-yellow-500 font-semibold">{{ number_format($rataRatingUlasan ?? 0, 1) }} ⭐</span>
        </p>
    </div>

    @if(!$limits['can_edit_foto'])
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="bg-orange-100 p-3 sm:p-4 rounded-full">
                <i class="fas fa-edit text-xl sm:text-2xl text-orange-500"></i>
            </div>
        </div>
        <p class="text-gray-500 text-xs sm:text-sm mb-1">Edit Requests</p>
        <h3 class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $pendingRequests }}</h3>
        <p class="text-xs text-gray-500 mt-2">Awaiting approval</p>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="bg-purple-100 p-3 sm:p-4 rounded-full">
                <i class="fas fa-video text-xl sm:text-2xl text-purple-500"></i>
            </div>
        </div>
        <p class="text-gray-500 text-xs sm:text-sm mb-1">Total Videos</p>
        <div class="flex items-baseline gap-2">
            <h3 class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalVideo }}</h3>
        </div>
    </div>
    @endif
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4">⚡ Quick Actions</h2>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        {{-- Add Destination --}}
        @if($jumlahDestinasi < ($limits['max_destinasi'] ?? PHP_INT_MAX))
        <a href="{{ route('pemilik.destinasi.create') }}"
           class="bg-green-50 hover:bg-green-100 border border-green-200 text-green-700 p-4 sm:p-5 rounded-xl transition flex items-center gap-3 sm:gap-4">
            <div class="bg-green-100 p-2 sm:p-3 rounded-full flex-shrink-0">
                <i class="fas fa-plus-circle text-xl sm:text-2xl text-green-500"></i>
            </div>
            <div>
                <p class="font-semibold text-sm sm:text-base">Add Destination</p>
                <p class="font-semibold text-xs text-green-500 mt-0.5">Create new destination</p>
            </div>
        </a>
        @else
        <div class="bg-gray-50 border border-gray-200 text-gray-400 p-4 sm:p-5 rounded-xl flex items-center gap-3 sm:gap-4 cursor-not-allowed">
            <div class="bg-gray-100 p-2 sm:p-3 rounded-full flex-shrink-0">
                <i class="fas fa-plus-circle text-xl sm:text-2xl"></i>
            </div>
            <div>
                <p class="font-semibold text-sm sm:text-base">Add Destination</p>
                <p class="font-semibold text-xs mt-0.5">Limit reached</p>
            </div>
        </div>
        @endif

        {{-- Reviews --}}
        <a href="{{ route('pemilik.ulasan.index') }}"
           class="bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 text-yellow-700 p-4 sm:p-5 rounded-xl transition flex items-center gap-3 sm:gap-4">
            <div class="bg-yellow-100 p-2 sm:p-3 rounded-full flex-shrink-0">
                <i class="fas fa-star text-xl sm:text-2xl text-yellow-500"></i>
            </div>
            <div>
                <p class="font-semibold text-sm sm:text-base">Reviews</p>
                <p class="font-semibold text-xs text-yellow-500 mt-0.5">{{ $totalUlasan ?? 0 }} reviews received</p>
            </div>
        </a>

        {{-- Promotion Banner --}}
        @if($isPremium)
        <a href="{{ route('pemilik.promosi.index') }}"
           class="bg-orange-50 hover:bg-orange-100 border border-orange-200 text-orange-700 p-4 sm:p-5 rounded-xl transition flex items-center gap-3 sm:gap-4">
            <div class="bg-orange-100 p-2 sm:p-3 rounded-full flex-shrink-0">
                <i class="fas fa-bullhorn text-xl sm:text-2xl text-orange-500"></i>
            </div>
            <div>
                <p class="font-semibold text-sm sm:text-base">Promo Banner</p>
                <p class="font-semibold text-xs text-orange-500 mt-0.5">Manage your promotions</p>
            </div>
        </a>
        @else
        <a href="{{ route('pemilik.paket.index') }}"
           class="bg-orange-50 hover:bg-orange-100 border border-orange-200 text-orange-400 p-4 sm:p-5 rounded-xl transition flex items-center gap-3 sm:gap-4">
            <div class="bg-orange-100 p-2 sm:p-3 rounded-full flex-shrink-0">
                <i class="fas fa-bullhorn text-xl sm:text-2xl text-orange-300"></i>
            </div>
            <div>
                <p class="font-semibold text-sm sm:text-base">Promo Banner</p>
                <p class="font-semibold text-xs text-orange-400 mt-0.5">Upgrade to Premium 🔒</p>
            </div>
        </a>
        @endif

        {{-- Upgrade Package --}}
        <a href="{{ route('pemilik.paket.index') }}"
           class="bg-purple-50 hover:bg-purple-100 border border-purple-200 text-purple-700 p-4 sm:p-5 rounded-xl transition flex items-center gap-3 sm:gap-4">
            <div class="bg-purple-100 p-2 sm:p-3 rounded-full flex-shrink-0">
                <i class="fas fa-rocket text-xl sm:text-2xl text-purple-500"></i>
            </div>
            <div>
                <p class="font-semibold text-sm sm:text-base">Upgrade Package</p>
                <p class="font-semibold text-xs text-purple-500 mt-0.5">Unlock more features</p>
            </div>
        </a>
    </div>
</div>

<!-- Recent Destinations -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg sm:text-xl font-bold text-gray-800">📍 Recent Destinations</h2>
    </div>

    @if($recentDestinasi->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full min-w-[580px]">
            <thead class="bg-gradient-to-r from-primary to-blue-400 text-white">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold">Name</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold">Category</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold">Price</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold">Photos/Videos</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold">Status</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($recentDestinasi as $destinasi)
                <tr class="hover:bg-accent/30 transition">
                    <td class="px-4 sm:px-6 py-4">
                        <div class="font-semibold text-gray-800 text-sm">{{ $destinasi->nama_destinasi }}</div>
                        @if($destinasi->is_featured)
                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">⭐ Featured</span>
                        @endif
                    </td>
                    <td class="px-4 sm:px-6 py-4">
                        <span class="bg-blue-100 text-blue-800 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">
                            {{ $destinasi->kategori->nama_kategori ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 sm:px-6 py-4 font-bold text-gray-700 text-sm whitespace-nowrap">
                        Rp {{ number_format($destinasi->harga, 0, ',', '.') }}
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                        {{ count($destinasi->foto ?? []) }} photos / {{ count($destinasi->video ?? []) }} videos
                    </td>
                    <td class="px-4 sm:px-6 py-4">
                        <span class="bg-green-100 text-green-800 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">
                            {{ ucfirst($destinasi->status) }}
                        </span>
                    </td>
                    <td class="px-4 sm:px-6 py-4">
                        <a href="{{ route('pemilik.destinasi.edit', $destinasi->id) }}"
                           class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="py-10 sm:py-12 px-6 text-center">
        <i class="fas fa-map-marked-alt text-4xl sm:text-5xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 mb-4 text-sm sm:text-base">No destinations yet</p>
        <a href="{{ route('pemilik.destinasi.create') }}"
           class="inline-block bg-primary hover:bg-blue-500 text-white px-6 py-3 rounded-lg transition text-sm sm:text-base">
            <i class="fas fa-plus mr-2"></i> Add First Destination
        </a>
    </div>
    @endif
</div>
@endsection