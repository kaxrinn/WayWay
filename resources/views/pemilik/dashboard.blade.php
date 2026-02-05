@extends('layouts.pemilik')

@section('title', 'Dashboard Pemilik Wisata')

@section('content')
<!-- Welcome Header -->
<div class="bg-gradient-to-r from-primary to-blue-400 rounded-xl shadow-lg p-8 mb-6 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-white/90">Kelola destinasi wisata Anda dengan mudah</p>
        </div>
        <div class="hidden md:block">
            <i class="fas fa-map-marked-alt text-8xl opacity-20"></i>
        </div>
    </div>
</div>

<!-- Paket Expired Warning -->
@if($isPaketExpired)
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
    <div class="flex items-center">
        <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
        <div>
            <p class="font-bold">Paket Anda Sudah Expired!</p>
            <p class="text-sm">Paket Anda expired pada {{ auth()->user()->paket_expired_at->format('d M Y') }}. 
                <a href="{{ route('pemilik.paket.index') }}" class="underline font-semibold">Upgrade sekarang</a> untuk melanjutkan.</p>
        </div>
    </div>
</div>
@endif

<!-- Paket Info Card -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-800">📦 Paket Aktif</h2>
        <a href="{{ route('pemilik.paket.index') }}" 
           class="text-primary hover:text-blue-600 font-semibold text-sm">
            Upgrade <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    @if($currentPaket)
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg">
            <p class="text-sm text-gray-600 mb-1">Paket</p>
            <p class="text-2xl font-bold text-blue-600">{{ $currentPaket->nama_paket }}</p>
        </div>
        
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg">
            <p class="text-sm text-gray-600 mb-1">Expired</p>
            <p class="text-lg font-bold text-green-600">
                {{ auth()->user()->paket_expired_at ? auth()->user()->paket_expired_at->format('d M Y') : 'Selamanya' }}
            </p>
        </div>
        
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg">
            <p class="text-sm text-gray-600 mb-1">Max Destinasi</p>
            <p class="text-2xl font-bold text-purple-600">
                {{ $limits['max_destinasi'] ?? '∞' }}
            </p>
        </div>
        
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 rounded-lg">
            <p class="text-sm text-gray-600 mb-1">Max Foto/Video</p>
            <p class="text-2xl font-bold text-yellow-600">
                {{ $limits['max_foto'] ?? '∞' }} / {{ $limits['max_video'] ?? '∞' }}
            </p>
        </div>
    </div>
    @else
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <p class="text-red-700 font-semibold">Paket belum diatur! Hubungi admin untuk aktivasi.</p>
    </div>
    @endif
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-blue-100 p-4 rounded-full">
                <i class="fas fa-map-marked-alt text-2xl text-blue-500"></i>
            </div>
        </div>
        <p class="text-gray-500 text-sm mb-1">Destinasi Aktif</p>
        <div class="flex items-baseline gap-2">
            <h3 class="text-3xl font-bold text-gray-800">{{ $jumlahDestinasi }}</h3>
            <span class="text-sm text-gray-500">/ {{ $limits['max_destinasi'] ?? '∞' }}</span>
        </div>
        @if($limits['max_destinasi'] && $jumlahDestinasi >= $limits['max_destinasi'])
            <p class="text-xs text-red-500 mt-2">Batas tercapai!</p>
        @endif
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-green-100 p-4 rounded-full">
                <i class="fas fa-images text-2xl text-green-500"></i>
            </div>
        </div>
        <p class="text-gray-500 text-sm mb-1">Total Foto</p>
        <div class="flex items-baseline gap-2">
            <h3 class="text-3xl font-bold text-gray-800">{{ $totalFoto }}</h3>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-purple-100 p-4 rounded-full">
                <i class="fas fa-video text-2xl text-purple-500"></i>
            </div>
        </div>
        <p class="text-gray-500 text-sm mb-1">Total Video</p>
        <div class="flex items-baseline gap-2">
            <h3 class="text-3xl font-bold text-gray-800">{{ $totalVideo }}</h3>
        </div>
    </div>
    
    @if(!$limits['can_edit_foto'])
    <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-yellow-100 p-4 rounded-full">
                <i class="fas fa-edit text-2xl text-yellow-500"></i>
            </div>
        </div>
        <p class="text-gray-500 text-sm mb-1">Edit Request</p>
        <h3 class="text-3xl font-bold text-gray-800">{{ $pendingRequests }}</h3>
        <p class="text-xs text-gray-500 mt-2">Menunggu approval</p>
    </div>
    @endif
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4">⚡ Quick Actions</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @if($jumlahDestinasi < ($limits['max_destinasi'] ?? PHP_INT_MAX))
        <a href="{{ route('pemilik.destinasi.create') }}" 
           class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white p-6 rounded-xl transition shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center gap-4">
            <i class="fas fa-plus-circle text-4xl"></i>
            <div>
                <p class="font-bold text-lg">Tambah Destinasi</p>
                <p class="text-sm text-green-100">Buat destinasi baru</p>
            </div>
        </a>
        @else
        <div class="bg-gray-200 text-gray-500 p-6 rounded-xl flex items-center gap-4 cursor-not-allowed">
            <i class="fas fa-plus-circle text-4xl"></i>
            <div>
                <p class="font-bold text-lg">Tambah Destinasi</p>
                <p class="text-sm">Batas tercapai - Upgrade paket</p>
            </div>
        </div>
        @endif
        
        <a href="{{ route('pemilik.paket.index') }}" 
           class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white p-6 rounded-xl transition shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center gap-4">
            <i class="fas fa-rocket text-4xl"></i>
            <div>
                <p class="font-bold text-lg">Upgrade Paket</p>
                <p class="text-sm text-purple-100">Tingkatkan fitur Anda</p>
            </div>
        </a>
        
        <a href="{{ route('pemilik.destinasi.index') }}" 
           class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white p-6 rounded-xl transition shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center gap-4">
            <i class="fas fa-list text-4xl"></i>
            <div>
                <p class="font-bold text-lg">Lihat Semua</p>
                <p class="text-sm text-blue-100">Kelola destinasi</p>
            </div>
        </a>
    </div>
</div>

<!-- Recent Destinasi -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-800">📍 Destinasi Terbaru</h2>
    </div>
    
    @if($recentDestinasi->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-primary to-blue-400 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Nama</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Kategori</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Harga</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Foto/Video</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($recentDestinasi as $destinasi)
                <tr class="hover:bg-accent/30 transition">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-800">{{ $destinasi->nama_destinasi }}</div>
                        @if($destinasi->is_featured)
                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">⭐ Featured</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">
                            {{ $destinasi->kategori->nama_kategori ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-700">
                        Rp {{ number_format($destinasi->harga, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ count($destinasi->foto ?? []) }} foto / {{ count($destinasi->video ?? []) }} video
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
                            {{ ucfirst($destinasi->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
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
    <div class="py-12 px-6 text-center">
        <i class="fas fa-map-marked-alt text-5xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 mb-4">Belum ada destinasi</p>
        <a href="{{ route('pemilik.destinasi.create') }}" 
           class="inline-block bg-primary hover:bg-blue-500 text-white px-6 py-3 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i> Tambah Destinasi Pertama
        </a>
    </div>
    @endif
</div>
@endsection