@extends('layouts.admin')

@section('title', 'Kelola Pemilik Wisata')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-user-tie text-primary"></i>
                Kelola Pemilik Wisata
            </h1>
            <p class="text-gray-500 mt-2">Manage akun pemilik wisata yang terdaftar di sistem</p>
        </div>
        <a href="{{ route('admin.pemilik.create') }}" 
           class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-lg transition shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Tambah Pemilik Wisata
        </a>
    </div>
</div>

<!-- Success Alert -->
@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow">
    <div class="flex items-center">
        <i class="fas fa-check-circle text-2xl mr-3"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
</div>
@endif

<!-- Table Card -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    @if($pemilikWisata->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-primary to-blue-400 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold">No</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Nama</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Email</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">No. Telepon</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Destinasi</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Terdaftar</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($pemilikWisata as $index => $pemilik)
                <tr class="hover:bg-accent/30 transition">
                    <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-primary/20 flex items-center justify-center mr-3">
                                <span class="text-primary font-bold text-sm">{{ strtoupper(substr($pemilik->name, 0, 2)) }}</span>
                            </div>
                            <span class="font-semibold text-gray-800">{{ $pemilik->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $pemilik->email }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $pemilik->no_telepon ?? '-' }}</td>
                    <td class="px-6 py-4">
    @if($pemilik->destinasi->isNotEmpty())
        <div class="flex flex-wrap gap-1">
            @foreach($pemilik->destinasi as $destinasi)
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">
                    {{ $destinasi->nama_destinasi }}
                </span>
            @endforeach
        </div>
    @else
        <span class="text-gray-400">Belum ada destinasi</span>
    @endif
</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $pemilik->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium flex items-center gap-1 w-fit">
                            <i class="fas fa-check-circle"></i>
                            Aktif
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.pemilik.edit', $pemilik->id) }}" 
                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg transition text-sm flex items-center gap-1">
                                <i class="fas fa-edit"></i>
                                Edit
                            </a>
                            <form method="POST" 
                                  action="{{ route('admin.pemilik.destroy', $pemilik->id) }}"
                                  onsubmit="return confirm('Yakin ingin menghapus pemilik wisata ini?')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition text-sm flex items-center gap-1">
                                    <i class="fas fa-trash"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <!-- Empty State -->
    <div class="py-20 px-6 text-center">
        <div class="flex flex-col items-center justify-center text-gray-400">
            <i class="fas fa-users-slash text-7xl mb-5"></i>
            <h3 class="text-2xl font-bold text-gray-600 mb-2">Belum Ada Pemilik Wisata</h3>
            <p class="text-gray-500 mb-6">Klik tombol "Tambah Pemilik Wisata" untuk menambahkan data baru</p>
            <a href="{{ route('admin.pemilik.create') }}" 
               class="bg-gradient-to-r from-primary to-blue-400 hover:from-blue-400 hover:to-primary text-white px-6 py-3 rounded-lg transition shadow-lg hover:shadow-xl flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Pemilik Wisata
            </a>
        </div>
    </div>
    @endif
</div>
@endsection