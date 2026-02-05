@extends('layouts.pemilik')

@section('title', 'Edit Request')

@section('content')
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
        <i class="fas fa-paper-plane text-blue-500"></i>
        Riwayat Edit Request Saya
    </h1>
    <p class="text-gray-500 mt-2">Daftar request edit yang Anda ajukan ke admin</p>
</div>

@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow">
    <div class="flex items-center">
        <i class="fas fa-check-circle text-2xl mr-3"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
</div>
@endif

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    @if($requests->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold">ID</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Destinasi</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Tipe Request</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Keterangan</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Tanggal</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Catatan Admin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($requests as $req)
                <tr class="hover:bg-accent/30 transition">
                    <td class="px-6 py-4 text-sm font-medium text-gray-700">{{ $req->id }}</td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-800">{{ $req->destinasi->nama_destinasi }}</div>
                        <div class="text-xs text-gray-500">{{ $req->destinasi->kategori->nama_kategori ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-700">
                            {{ ucfirst(str_replace('_', ' ', $req->request_type)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                        {{ $req->keterangan }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $req->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($req->status == 'pending')
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-medium">
                                <i class="fas fa-clock mr-1"></i>Pending
                            </span>
                        @elseif($req->status == 'approved')
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
                                <i class="fas fa-check-circle mr-1"></i>Approved
                            </span>
                        @else
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">
                                <i class="fas fa-times-circle mr-1"></i>Rejected
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($req->admin_notes)
                            <div class="max-w-xs truncate" title="{{ $req->admin_notes }}">
                                {{ $req->admin_notes }}
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
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
            <i class="fas fa-inbox text-7xl mb-5"></i>
            <h3 class="text-2xl font-bold text-gray-600 mb-2">Belum Ada Request</h3>
            <p class="text-gray-500">Anda belum pernah mengajukan request edit</p>
            
            <a href="{{ route('pemilik.destinasi.index') }}" 
               class="mt-6 bg-primary hover:bg-blue-500 text-white px-6 py-3 rounded-lg transition shadow-lg inline-flex items-center gap-2">
                <i class="fas fa-map-marked-alt"></i>
                Lihat Destinasi Saya
            </a>
        </div>
    </div>
    @endif
</div>

<!-- Info Box -->
<div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mt-6">
    <p class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
        <i class="fas fa-info-circle text-blue-600"></i>
        Tentang Edit Request:
    </p>
    <ul class="text-sm text-gray-600 space-y-1 ml-6">
        <li>• Paket <strong>Basic</strong> tidak bisa edit destinasi langsung</li>
        <li>• Request akan diproses oleh admin dalam 1-2 hari kerja</li>
        <li>• Jika approved, perubahan akan otomatis diterapkan</li>
        <li>• Upgrade ke <strong>Standard/Premium</strong> untuk edit langsung tanpa approval</li>
    </ul>
    
    <a href="{{ route('pemilik.paket.index') }}" 
       class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm transition">
        <i class="fas fa-rocket mr-1"></i> Lihat Paket
    </a>
</div>
@endsection