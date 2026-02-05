@extends('layouts.admin')

@section('title', 'Kelola Promosi')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-bullhorn text-purple-500"></i>
            Kelola Promosi & Iklan
        </h1>
        <p class="text-gray-500 mt-2">Manage promosi dan iklan destinasi wisata</p>
    </div>
</div>

<!-- Paket Promosi Info Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    @foreach($paketPromosi as $paket)
    <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg p-6 border-2 border-purple-400 hover:shadow-2xl transition transform hover:-translate-y-2">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-2xl font-bold text-gray-800">{{ $paket->nama_paket }}</h3>
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fas fa-star text-purple-500 text-xl"></i>
            </div>
        </div>
        
        <div class="mb-4">
            <p class="text-4xl font-bold text-purple-600">
                Rp {{ number_format($paket->harga, 0, ',', '.') }}
            </p>
            <p class="text-sm text-gray-500">Per bulan</p>
        </div>
        
        <p class="text-gray-600 text-sm mb-4">{{ $paket->deskripsi }}</p>
        
        <div class="border-t border-gray-200 pt-4">
            <p class="text-xs font-semibold text-gray-700 mb-2">Fitur:</p>
            <div class="space-y-1">
                <p class="text-xs text-gray-600 flex items-start gap-2">
                    <i class="fas fa-check text-green-500 mt-0.5"></i>
                    <span>Max {{ $paket->max_destinasi }} destinasi</span>
                </p>
                <p class="text-xs text-gray-600 flex items-start gap-2">
                    <i class="fas fa-check text-green-500 mt-0.5"></i>
                    <span>Max {{ $paket->max_foto }} foto per destinasi</span>
                </p>
                <p class="text-xs text-gray-600 flex items-start gap-2">
                    <i class="fas fa-check text-green-500 mt-0.5"></i>
                    <span>Max {{ $paket->max_video }} video per destinasi</span>
                </p>
                <p class="text-xs text-gray-600 flex items-start gap-2">
                    <i class="fas fa-{{ $paket->can_edit_foto ? 'check' : 'times' }} text-{{ $paket->can_edit_foto ? 'green' : 'red' }}-500 mt-0.5"></i>
                    <span>{{ $paket->can_edit_foto ? 'Edit langsung' : 'Butuh persetujuan admin' }}</span>
                </p>
                @if($paket->is_featured_allowed)
                <p class="text-xs text-gray-600 flex items-start gap-2">
                    <i class="fas fa-check text-green-500 mt-0.5"></i>
                    <span>Featured listing</span>
                </p>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Active Promosi Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-800">Daftar Promosi Aktif</h2>
        <p class="text-sm text-gray-500 mt-1">Promosi yang sedang berjalan di platform</p>
    </div>
    
    @if($promosi->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-purple-500 to-purple-600 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold">ID</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">User</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Paket</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Harga</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Periode</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($promosi as $item)
                @php
                    $today = now();
                    $isExpired = $item->tanggal_selesai < $today;
                    $isActive = $item->tanggal_mulai <= $today && $item->tanggal_selesai >= $today;
                @endphp
                <tr class="hover:bg-accent/30 transition">
                    <td class="px-6 py-4 text-sm font-medium text-gray-700">{{ $item->id }}</td>
                    <td class="px-6 py-4">
                        @if($item->user)
                        <div class="font-semibold text-gray-800">{{ $item->user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $item->user->email }}</div>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-medium">
                            {{ $item->paket->nama_paket }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-700">
                        Rp {{ number_format($item->paket->harga, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <div>{{ $item->tanggal_mulai->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">s/d {{ $item->tanggal_selesai->format('d M Y') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($isExpired)
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">
                                <i class="fas fa-times-circle mr-1"></i>Expired
                            </span>
                        @elseif($isActive)
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
                                <i class="fas fa-check-circle mr-1"></i>Active
                            </span>
                        @else
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-medium">
                                <i class="fas fa-clock mr-1"></i>Pending
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="py-20 px-6 text-center">
        <div class="flex flex-col items-center justify-center text-gray-400">
            <i class="fas fa-bullhorn text-7xl mb-5"></i>
            <h3 class="text-2xl font-bold text-gray-600 mb-2">Belum Ada Promosi</h3>
            <p class="text-gray-500">Belum ada promosi aktif saat ini</p>
        </div>
    </div>
    @endif
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
    <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Total Promosi</p>
                <h3 class="text-3xl font-bold text-purple-500">{{ $promosi->count() }}</h3>
            </div>
            <div class="bg-purple-100 p-4 rounded-full">
                <i class="fas fa-bullhorn text-2xl text-purple-500"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Promosi Aktif</p>
                <h3 class="text-3xl font-bold text-green-500">
                    {{ $promosi->filter(fn($p) => $p->tanggal_mulai <= now() && $p->tanggal_selesai >= now())->count() }}
                </h3>
            </div>
            <div class="bg-green-100 p-4 rounded-full">
                <i class="fas fa-check-circle text-2xl text-green-500"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Promosi Expired</p>
                <h3 class="text-3xl font-bold text-red-500">
                    {{ $promosi->filter(fn($p) => $p->tanggal_selesai < now())->count() }}
                </h3>
            </div>
            <div class="bg-red-100 p-4 rounded-full">
                <i class="fas fa-times-circle text-2xl text-red-500"></i>
            </div>
        </div>
    </div>
</div>
@endsection