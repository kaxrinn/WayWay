@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Card -->
<div class="bg-gradient-to-r from-accent to-yellow-200 rounded-2xl p-8 mb-6 shadow-lg">
    <h1 class="text-3xl font-bold text-gray-800">Halo Admin!</h1>
    <p class="text-gray-700 mt-2">Selamat datang, <strong>{{ auth()->user()->name }}</strong></p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Destinasi -->
    <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Destinasi</p>
                <h3 class="text-4xl font-bold text-primary">{{ $stats['total_destinasi'] }}</h3>
                <p class="text-gray-600 text-sm mt-2">Jumlah destinasi wisata saat ini</p>
            </div>
            <div class="bg-primary/10 p-4 rounded-full">
                <i class="fas fa-map-marked-alt text-3xl text-primary"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Kategori -->
    <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Kategori</p>
                <h3 class="text-4xl font-bold text-green-500">{{ $stats['total_kategori'] }}</h3>
                <p class="text-gray-600 text-sm mt-2">Jumlah kategori wisata saat ini</p>
            </div>
            <div class="bg-green-100 p-4 rounded-full">
                <i class="fas fa-tags text-3xl text-green-500"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Wisatawan -->
    <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Wisatawan</p>
                <h3 class="text-4xl font-bold text-blue-500">{{ $stats['total_wisatawan'] }}</h3>
                <p class="text-gray-600 text-sm mt-2">Total pengguna wisatawan</p>
            </div>
            <div class="bg-blue-100 p-4 rounded-full">
                <i class="fas fa-users text-3xl text-blue-500"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Pemilik Wisata -->
    <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Pemilik Wisata</p>
                <h3 class="text-4xl font-bold text-purple-500">{{ $stats['total_pemilik'] }}</h3>
                <p class="text-gray-600 text-sm mt-2">Total pemilik wisata terdaftar</p>
            </div>
            <div class="bg-purple-100 p-4 rounded-full">
                <i class="fas fa-user-tie text-3xl text-purple-500"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Destinations Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Destinasi Terbaru</h2>
            <p class="text-sm text-gray-500 mt-1">10 destinasi yang baru ditambahkan</p>
        </div>
        <a href="{{ route('admin.destinasi.index') }}" 
           class="bg-primary hover:bg-primary/80 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-eye"></i>
            Lihat Semua
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-primary to-blue-400 text-white">
            <tr>
            <th class="px-6 py-3 text-left text-sm font-semibold">ID</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Foto</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Nama Destinasi</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Kategori</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Harga</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
           </tr>
        </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse($recent_destinasi as $destinasi)
                <tr class="hover:bg-accent/30 transition">
                <td class="px-6 py-4 text-sm text-gray-700">
               {{ $destinasi->id }}
             </td>

    {{-- FOTO --}}
    <td class="px-6 py-4">
       @php
        $foto = $destinasi->foto;

       // kalau foto berupa JSON / array
         if (is_string($foto) && str_starts_with($foto, '[')) {
           $foto = json_decode($foto, true)[0] ?? null;
        }
    @endphp

     @if($foto)
      <img src="{{ Storage::url($foto) }}"
         alt="{{ $destinasi->nama_destinasi }}"
         class="h-14 w-20 rounded-lg object-cover shadow">
        @else
         <div class="h-14 w-20 bg-gray-200 rounded-lg flex items-center justify-center">
            <i class="fas fa-image text-gray-400 text-xl"></i>
         </div>
      @endif
    </td>

    {{-- NAMA --}}
    <td class="px-6 py-4 font-medium text-gray-800">
        {{ $destinasi->nama_destinasi }}
    </td>

    {{-- KATEGORI --}}
    <td class="px-6 py-4 text-sm text-gray-700">
        @if($destinasi->kategori)
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">
                {{ $destinasi->kategori->nama_kategori }}
            </span>
        @else
            <span class="text-gray-400">-</span>
        @endif
    </td>

    {{-- HARGA --}}
    <td class="px-6 py-4 text-sm font-semibold text-gray-700">
        Rp {{ number_format($destinasi->harga, 0, ',', '.') }}
    </td>

    {{-- STATUS --}}
    <td class="px-6 py-4">
        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
            <i class="fas fa-check-circle mr-1"></i> Active
        </span>
    </td>
</tr>

                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <i class="fas fa-inbox text-5xl mb-3"></i>
                            <p class="text-lg font-medium">Belum ada destinasi</p>
                            <p class="text-sm">Tambahkan destinasi pertama Anda</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection