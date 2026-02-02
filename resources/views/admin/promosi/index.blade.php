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
    <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg p-6 border-2 border-{{ $loop->first ? 'blue' : ($loop->iteration == 2 ? 'purple' : 'yellow') }}-400 hover:shadow-2xl transition transform hover:-translate-y-2">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-2xl font-bold text-gray-800">{{ $paket->nama_paket }}</h3>
            <div class="bg-{{ $loop->first ? 'blue' : ($loop->iteration == 2 ? 'purple' : 'yellow') }}-100 p-3 rounded-full">
                <i class="fas fa-star text-{{ $loop->first ? 'blue' : ($loop->iteration == 2 ? 'purple' : 'yellow') }}-500 text-xl"></i>
            </div>
        </div>
        
        <div class="mb-4">
            <p class="text-4xl font-bold text-{{ $loop->first ? 'blue' : ($loop->iteration == 2 ? 'purple' : 'yellow') }}-600">
                Rp {{ number_format($paket->harga, 0, ',', '.') }}
            </p>
            <p class="text-sm text-gray-500">Durasi: {{ $paket->durasi_hari }} hari</p>
        </div>
        
        <p class="text-gray-600 text-sm mb-4">{{ $paket->deskripsi }}</p>
        
        <div class="border-t border-gray-200 pt-4">
            <p class="text-xs font-semibold text-gray-700 mb-2">Fitur:</p>
            @foreach(explode(',', $paket->fitur) as $fitur)
            <p class="text-xs text-gray-600 mb-1 flex items-start gap-2">
                <i class="fas fa-check text-green-500 mt-0.5"></i>
                <span>{{ trim($fitur) }}</span>
            </p>
            @endforeach
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
                    <th class="px-6 py-4 text-left text-sm font-semibold">Destinasi</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Paket</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Harga</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Periode</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($promosi as $promosi)
                @php
                    $today = now();
                    $isExpired = $promosi->tanggal_selesai < $today;
                    $isActive = $promosi->tanggal_mulai <= $today && $promosi->tanggal_selesai >= $today;
                @endphp
                <tr class="hover:bg-accent/30 transition">
                    <td class="px-6 py-4 text-sm font-medium text-gray-700">{{ $promosi->id }}</td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-800">{{ $promosi->destinasi->nama_destinasi ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $promosi->destinasi->kategori->nama_kategori ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-medium">
                            {{ $promosi->paket->nama_paket }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-700">
                        Rp {{ number_format($promosi->paket->harga, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <div>{{ $promosi->tanggal_mulai->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">s/d {{ $promosi->tanggal_selesai->format('d M Y') }}</div>
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