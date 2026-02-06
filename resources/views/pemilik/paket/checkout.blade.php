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
            
            <!-- Midtrans Payment Info -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-6">
                <p class="text-sm text-gray-700">
                    <i class="fas fa-shield-alt text-blue-500 mr-2"></i>
                    Pembayaran aman menggunakan <strong>Midtrans</strong>
                </p>
                <p class="text-xs text-gray-600 mt-2">
                    Support: Credit Card, GoPay, OVO, Bank Transfer, QRIS, dan lainnya
                </p>
            </div>
            
            <!-- Midtrans Pay Button -->
            <button id="pay-button" 
                    class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-4 rounded-lg font-bold text-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                <i class="fas fa-lock mr-2"></i>
                Bayar Sekarang dengan Midtrans
            </button>
            
            <p class="text-center text-sm text-gray-500 mt-4">
                Klik tombol di atas untuk melanjutkan ke halaman pembayaran
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Midtrans Snap Script (SANDBOX) -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" 
        data-client-key="{{ $clientKey }}"></script>

<script>
const payButton = document.getElementById('pay-button');

payButton.addEventListener('click', function () {
    snap.pay('{{ $snapToken }}', {
        onSuccess: function(result) {
            console.log('Payment success:', result);
            window.location.href = '{{ route("pemilik.paket.callback") }}';
        },
        onPending: function(result) {
            console.log('Payment pending:', result);
            window.location.href = '{{ route("pemilik.paket.callback") }}';
        },
        onError: function(result) {
            console.log('Payment error:', result);
            alert('Pembayaran gagal! Silakan coba lagi.');
        },
        onClose: function() {
            console.log('Payment popup closed');
            alert('Anda menutup popup pembayaran. Silakan klik tombol bayar lagi jika ingin melanjutkan.');
        }
    });
});
</script>
@endpush