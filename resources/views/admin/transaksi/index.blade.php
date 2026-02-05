@extends('layouts.admin')

@section('title', 'Transaksi Promosi')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
        <i class="fas fa-receipt text-green-500"></i>
        Transaksi Promosi
    </h1>
    <p class="text-gray-500 mt-2">Kelola semua transaksi pembelian paket promosi</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Total Transaksi</p>
                <h3 class="text-3xl font-bold text-blue-600">{{ $stats['total'] }}</h3>
            </div>
            <div class="bg-blue-100 p-4 rounded-full">
                <i class="fas fa-receipt text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Pending</p>
                <h3 class="text-3xl font-bold text-yellow-600">Rp {{ number_format($stats['pending'], 0, ',', '.') }}</h3>
            </div>
            <div class="bg-yellow-100 p-4 rounded-full">
                <i class="fas fa-clock text-2xl text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Berhasil</p>
                <h3 class="text-3xl font-bold text-green-600">Rp {{ number_format($stats['success'], 0, ',', '.') }}</h3>
            </div>
            <div class="bg-green-100 p-4 rounded-full">
                <i class="fas fa-check-circle text-2xl text-green-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Transaksi Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-primary to-blue-400 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">ID</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">User</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Paket</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Total</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Metode</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Tanggal</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($transaksis as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">{{ $item->id }}</td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $item->user->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $item->user->email ?? '-' }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-semibold">
                            {{ $item->paket->nama_paket ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-800">
                        Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm capitalize">
                        {{ $item->metode_pembayaran }}
                    </td>
                    <td class="px-6 py-4">
                        @if($item->status_pembayaran == 'pending')
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                        @elseif($item->status_pembayaran == 'success')
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-check-circle"></i> Success
                            </span>
                        @else
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-times-circle"></i> Failed
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $item->tanggal_transaksi->format('d M Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
    @if($item->status_pembayaran == 'pending')
        <div class="flex gap-2">

            <!-- APPROVE -->
            <form method="POST" action="{{ route('admin.transaksi.approve', $item->id) }}">
                @csrf
                <button type="submit"
                    class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-xs font-semibold">
                    <i class="fas fa-check"></i> Approve
                </button>
            </form>

            <!-- REJECT -->
            <form method="POST" action="{{ route('admin.transaksi.reject', $item->id) }}">
                @csrf
                <button type="submit"
                    class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-xs font-semibold">
                    <i class="fas fa-times"></i> Reject
                </button>
            </form>

        </div>
    @else
        <span class="text-gray-400 text-xs italic">-</span>
    @endif
</td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center text-gray-400">
                            <i class="fas fa-receipt text-6xl mb-3"></i>
                            <p class="text-lg font-medium">Belum ada transaksi</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection