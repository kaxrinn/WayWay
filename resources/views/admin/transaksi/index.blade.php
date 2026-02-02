@extends('layouts.admin')

@section('title', 'Kelola Transaksi')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-receipt text-green-500"></i>
            Kelola Transaksi Promosi
        </h1>
        <p class="text-gray-500 mt-2">Monitor semua transaksi pembelian paket promosi</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 shadow-lg text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-blue-100 text-sm mb-1">Total Transaksi</p>
                <h3 class="text-4xl font-bold">{{ $stats['total'] }}</h3>
            </div>
            <div class="bg-white/20 p-4 rounded-full">
                <i class="fas fa-shopping-cart text-3xl"></i>
            </div>
        </div>
        <p class="text-blue-100 text-sm">Semua transaksi</p>
    </div>
    
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-6 shadow-lg text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-yellow-100 text-sm mb-1">Pending</p>
                <h3 class="text-4xl font-bold">Rp {{ number_format($stats['pending'], 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white/20 p-4 rounded-full">
                <i class="fas fa-clock text-3xl"></i>
            </div>
        </div>
        <p class="text-yellow-100 text-sm">Menunggu pembayaran</p>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 shadow-lg text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-green-100 text-sm mb-1">Berhasil</p>
                <h3 class="text-4xl font-bold">Rp {{ number_format($stats['success'], 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white/20 p-4 rounded-full">
                <i class="fas fa-check-circle text-3xl"></i>
            </div>
        </div>
        <p class="text-green-100 text-sm">Total pendapatan</p>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="relative">
            <input type="text" 
                   id="searchInput"
                   placeholder="🔍 Cari transaksi..."
                   class="w-full px-6 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition pl-12">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
        
        <select id="filterStatus" class="px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition">
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="success">Success</option>
            <option value="failed">Failed</option>
            <option value="refund">Refund</option>
        </select>
        
        <select id="filterPaket" class="px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition">
            <option value="">Semua Paket</option>
            @foreach(\App\Models\PaketPromosi::all() as $paket)
                <option value="{{ $paket->id }}">{{ $paket->nama_paket }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Transaksi Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    @if($transaksi->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full" id="transaksiTable">
            <thead class="bg-gradient-to-r from-green-500 to-green-600 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold">ID</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Pembeli</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Email</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Destinasi</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Paket</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Total</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Metode</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Tanggal</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($transaksi as $transaksi)
                <tr class="hover:bg-accent/30 transition transaksi-row" data-status="{{ $transaksi->status_pembayaran }}" data-paket="{{ $transaksi->paket_id }}">
                    <td class="px-6 py-4 text-sm font-medium text-gray-700">{{ $transaksi->id }}</td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-800">{{ $transaksi->user->name }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $transaksi->user->email }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-800">{{ $transaksi->promosi->destinasi->nama_destinasi ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-medium">
                            {{ $transaksi->paket->nama_paket }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-700">
                        Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $transaksi->metode_pembayaran ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $transaksi->tanggal_transaksi->format('d M Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'pending' => 'yellow',
                                'success' => 'green',
                                'failed' => 'red',
                                'refund' => 'gray'
                            ];
                            $color = $statusColors[$transaksi->status_pembayaran] ?? 'gray';
                        @endphp
                        <span class="bg-{{ $color }}-100 text-{{ $color }}-800 px-3 py-1 rounded-full text-xs font-medium">
                            {{ ucfirst($transaksi->status_pembayaran) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="py-20 px-6 text-center">
        <div class="flex flex-col items-center justify-center text-gray-400">
            <i class="fas fa-receipt text-7xl mb-5"></i>
            <h3 class="text-2xl font-bold text-gray-600 mb-2">Belum Ada Transaksi</h3>
            <p class="text-gray-500">Belum ada transaksi promosi yang tercatat</p>
        </div>
    </div>
    @endif
</div>

<!-- Summary Card -->
<div class="mt-6 bg-gradient-to-r from-primary to-blue-400 rounded-xl p-6 shadow-lg text-white">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-semibold mb-2">Total Pendapatan dari Promosi</h3>
            <p class="text-3xl font-bold">Rp {{ number_format($stats['success'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white/20 p-4 rounded-full">
            <i class="fas fa-coins text-4xl"></i>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('.transaksi-row');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});

// Filter by status
document.getElementById('filterStatus').addEventListener('change', function() {
    const status = this.value;
    const rows = document.querySelectorAll('.transaksi-row');
    
    rows.forEach(row => {
        if (!status || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Filter by paket
document.getElementById('filterPaket').addEventListener('change', function() {
    const paket = this.value;
    const rows = document.querySelectorAll('.transaksi-row');
    
    rows.forEach(row => {
        if (!paket || row.dataset.paket === paket) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endpush