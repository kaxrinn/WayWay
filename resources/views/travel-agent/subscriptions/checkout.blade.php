@extends('layouts.travel-agent')

@section('title', 'Checkout')

@section('content')
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-8 py-6">
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-credit-card"></i>
                Checkout Package {{ $package->nama_paket }}
            </h1>
            <p class="text-blue-100 mt-2">Complete the payment to activate the subscription package</p>
        </div>

        <!-- Order Summary -->
        <div class="p-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl text-white p-6 mb-8">
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <p class="text-blue-100 text-sm">Package</p>
                        <p class="text-2xl font-bold">{{ $package->nama_paket }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-blue-100 text-sm">Price</p>
                        <p class="text-2xl font-bold">Rp {{ number_format($package->harga, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="border-t border-blue-400 pt-6">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold">Total Payment</span>
                        <span class="text-4xl font-bold">Rp {{ number_format($package->harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div class="border-l-4 border-blue-500 pl-4">
                    <p class="text-gray-500 text-sm">Package Duration</p>
                    <p class="text-xl font-bold text-gray-800">
                        @if($package->durasi_bulan == 0)
                            Lifetime
                        @else
                            {{ $package->durasi_bulan }} months
                        @endif
                    </p>
                </div>

                <div class="border-l-4 border-green-500 pl-4">
                    <p class="text-gray-500 text-sm">Max Tour Packages</p>
                    <p class="text-xl font-bold text-gray-800">{{ $package->max_packages }}</p>
                </div>

                <div class="border-l-4 border-purple-500 pl-4">
                    <p class="text-gray-500 text-sm">Active From</p>
                    <p class="text-lg font-bold text-gray-800">Today</p>
                </div>

                <div class="border-l-4 border-orange-500 pl-4">
                    <p class="text-gray-500 text-sm">Payment Method</p>
                    <p class="text-lg font-bold text-gray-800">Midtrans</p>
                </div>
            </div>

            <!-- Features -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <p class="font-bold text-gray-800 mb-4">Features You Will Get:</p>
                <div class="grid grid-cols-2 gap-3">
                    @if($package->fitur)
                        @foreach($package->fitur as $fitur)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            {{ ucfirst(str_replace('_', ' ', $fitur)) }}
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Midtrans Button -->
            <div id="snap-container"></div>

            <!-- Alternative: Back Button -->
            <div class="mt-8 text-center">
                <a href="{{ route('travel-agent.subscriptions.upgrade') }}" class="text-gray-600 hover:text-gray-800">
                    Cancel and go back to package options
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Midtrans Snap JS -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
<script>
    snap.pay('{{ $snapToken }}', {
        onSuccess: function(result) {
            console.log("Payment Success", result);
            window.location.href = "{{ route('travel-agent.subscriptions.callback') }}";
        },
        onPending: function(result) {
            console.log("Payment Pending", result);
            window.location.href = "{{ route('travel-agent.subscriptions.callback') }}";
        },
        onError: function(result) {
            console.log("Payment Error", result);
            alert("Payment failed. Please try again.");
            window.location.href = "{{ route('travel-agent.subscriptions.upgrade') }}";
        },
        onClose: function() {
            console.log("Snap closed");
            window.location.href = "{{ route('travel-agent.subscriptions.upgrade') }}";
        }
    });
</script>
@endsection