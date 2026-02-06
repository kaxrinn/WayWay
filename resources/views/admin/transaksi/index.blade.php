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

@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-4">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
</div>
@endif

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
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">User</th>
                    <th class="px-6 py-3">Paket</th>
                    <th class="px-6 py-3">Total</th>
                    <th class="px-6 py-3">Metode</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($transaksis as $item)
                <tr>
                    <td class="px-6 py-4">{{ $item->id }}</td>
                    <td class="px-6 py-4">{{ $item->user->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $item->paket->nama_paket ?? '-' }}</td>
                    <td class="px-6 py-4">Rp {{ number_format($item->total_harga,0,',','.') }}</td>
                    <td class="px-6 py-4">{{ $item->metode_pembayaran }}</td>
                    <td class="px-6 py-4">
                        @if($item->status_pembayaran == 'pending')
                            <span class="bg-yellow-100 px-3 py-1 rounded">Pending</span>
                        @elseif($item->status_pembayaran == 'success')
                            <span class="bg-green-100 px-3 py-1 rounded">Success</span>
                        @else
                            <span class="bg-red-100 px-3 py-1 rounded">Failed</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $item->tanggal_transaksi->format('d M Y H:i') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">

                            @if($item->status_pembayaran == 'pending')
                                <form method="POST" action="{{ route('admin.transaksi.approve', $item->id) }}">
                                    @csrf
                                    <button class="bg-green-500 text-white px-3 py-1 rounded text-xs">Terima</button>
                                </form>

                                <form method="POST" action="{{ route('admin.transaksi.reject', $item->id) }}">
                                    @csrf
                                    <button class="bg-red-500 text-white px-3 py-1 rounded text-xs">Tolak</button>
                                </form>
                            @endif

                            <!-- DELETE (FIXED) -->
                            <form action="{{ route('admin.transaksi.destroy', $item->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin mau hapus transaksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="bg-orange-500 text-white px-3 py-1 rounded text-xs">
                                    Hapus
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                        Belum ada transaksi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="p-4">
        {{ $transaksis->links('pagination::tailwind') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDeleteModal(id) {
    document.getElementById('deleteForm').action = `/admin/transaksi/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>
@endpush
