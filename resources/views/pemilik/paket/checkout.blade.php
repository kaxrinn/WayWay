@extends('layouts.pemilik')

@section('title', 'Checkout Paket')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary to-blue-400 px-8 py-6">
            <h1 class="text-3xl font-bold text-white">💳 Checkout Paket {{ $paket->nama_paket }}</h1>
        </div>
        
        <!-- Order Summary -->
        <div class="p-8">
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h3 class="font-bold text-lg mb-4">Ringkasan Pesanan</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>Paket:</span>
                        <span class="font-semibold">{{ $paket->nama_paket }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Durasi:</span>
                        <span class="font-semibold">{{ $durasi == 'monthly' ? '1 Bulan' : '1 Tahun' }}</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold text-primary pt-2 border-t">
                        <span>Total:</span>
                        <span>Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Payment Instructions -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-6">
                <h4 class="font-bold mb-2">Transfer ke Rekening:</h4>
                <p class="font-mono text-lg">BCA: 1234567890</p>
                <p class="text-sm text-gray-600">a/n PT WayWay Indonesia</p>
            </div>
            
            <!-- Upload Bukti Form -->
            <form method="POST" action="{{ route('pemilik.transaksi.confirm', $transaksi->id) }}" enctype="multipart/form-data">
                @csrf
                
                <label class="block font-semibold mb-2">Upload Bukti Transfer:</label>
                <input type="file" name="bukti_pembayaran" accept="image/*" required
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg">
                
                <button type="submit" class="w-full mt-6 bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 rounded-lg font-semibold">
                    <i class="fas fa-check mr-2"></i> Konfirmasi Pembayaran
                </button>
            </form>
        </div>
    </div>
</div>
@endsection