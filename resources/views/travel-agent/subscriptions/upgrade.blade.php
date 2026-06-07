@extends('layouts.travel-agent')

@section('title', 'Upgrade Package')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
        <i class="fas fa-arrow-up text-green-500"></i>
        Upgrade Subscription Package
    </h1>
    <p class="text-gray-500 mt-2">Choose the right package to boost your business visibility</p>
</div>

<!-- Success Alert -->
@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow">
    <i class="fas fa-check-circle mr-2"></i>
    {{ session('success') }}
</div>
@endif

<!-- Error Alert -->
@if(session('error'))
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 shadow">
    <i class="fas fa-exclamation-circle mr-2"></i>
    {{ session('error') }}
</div>
@endif

<!-- Packages Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
    @forelse($packages as $package)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:scale-105 {{ in_array($package->id, $currentSubscriptions) ? 'ring-4 ring-green-400' : '' }}">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6">
            <h3 class="text-2xl font-bold">{{ $package->nama_paket }}</h3>
            <p class="text-blue-100 text-sm mt-1">{{ $package->deskripsi }}</p>
        </div>

        <!-- Price -->
        <div class="p-6 border-b bg-gray-50">
            @if($package->harga == 0)
                <p class="text-4xl font-bold text-green-600">FREE 🎉</p>
                <p class="text-gray-600 text-sm">Forever</p>
            @else
                <div class="flex items-baseline gap-2">
                    <p class="text-4xl font-bold text-gray-800">Rp {{ number_format($package->harga, 0, ',', '.') }}</p>
                    <p class="text-gray-600">/month</p>
                </div>
                <p class="text-gray-600 text-sm mt-1">Duration: {{ $package->durasi_bulan }} months</p>
            @endif
        </div>

        <!-- Features -->
        <div class="p-6 space-y-3 border-b">
            <p class="font-bold text-gray-800 text-sm">Package Features:</p>
            <div class="space-y-2">
                <!-- Max Packages Feature -->
                <div class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                    <div>
                        <p class="font-semibold text-gray-800">Max Tour Packages</p>
                        <p class="text-gray-600 text-sm">{{ $package->max_packages }} packages can be created</p>
                    </div>
                </div>

                <!-- Other Features -->
                @if($package->fitur)
                    @foreach($package->fitur as $fitur)
                    <div class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                        <p class="text-gray-700 text-sm">{{ ucfirst(str_replace('_', ' ', $fitur)) }}</p>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Status & Button -->
        <div class="p-6">
            @if(in_array($package->id, $currentSubscriptions))
                <!-- Already have this package -->
                <div class="bg-green-100 border border-green-300 rounded-lg p-3 text-center">
                    <p class="text-green-800 font-semibold text-sm">
                        <i class="fas fa-check-circle mr-2"></i> ALREADY OWNED
                    </p>
                </div>
            @else
                <!-- Button to buy -->
                @if($package->harga == 0)
                    <!-- Free package - direct activate -->
                    <form method="POST" action="{{ route('travel-agent.subscriptions.checkout', $package->id) }}">
                        @csrf
                        <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-lg font-bold hover:shadow-lg transition">
                            <i class="fas fa-check-circle mr-2"></i> Activate Free Package
                        </button>
                    </form>
                @else
                    <!-- Paid package - go to Midtrans -->
                    <form method="POST" action="{{ route('travel-agent.subscriptions.checkout', $package->id) }}">
                        @csrf
                        <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:shadow-lg transition">
                            <i class="fas fa-credit-card mr-2"></i> Pay Now
                        </button>
                    </form>
                @endif
            @endif
        </div>

        <!-- Current Package Badge -->
        @if(in_array($package->id, $currentSubscriptions))
        <div class="bg-green-500 text-white px-3 py-1 text-xs font-bold absolute top-4 right-4 rounded">
            ACTIVE ✓
        </div>
        @endif
    </div>
    @empty
    <!-- No packages -->
    <div class="col-span-3 bg-white rounded-xl shadow-lg p-12 text-center">
        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
        <p class="text-xl font-bold text-gray-800">No packages available</p>
        <p class="text-gray-600 mt-2">Contact admin for more information</p>
    </div>
    @endforelse
</div>

<!-- Comparison Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden mt-8">
    <div class="bg-gray-50 px-6 py-4 border-b">
        <h3 class="text-lg font-bold text-gray-800">
            <i class="fas fa-table text-blue-500 mr-2"></i>
            Package Comparison
        </h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-gray-800">Feature</th>
                    @foreach($packages as $package)
                    <th class="px-6 py-3 text-center font-semibold text-gray-800">{{ $package->nama_paket }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <!-- Max Packages Row -->
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-semibold text-gray-800">Max Tour Packages</td>
                    @foreach($packages as $package)
                    <td class="px-6 py-4 text-center text-gray-800 font-bold">{{ $package->max_packages }}</td>
                    @endforeach
                </tr>

                <!-- Price Row -->
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-semibold text-gray-800">Price</td>
                    @foreach($packages as $package)
                    <td class="px-6 py-4 text-center">
                        @if($package->harga == 0)
                            <span class="text-green-600 font-bold">FREE</span>
                        @else
                            <span class="text-gray-800 font-bold">Rp {{ number_format($package->harga, 0, ',', '.') }}</span>
                        @endif
                    </td>
                    @endforeach
                </tr>

                <!-- Duration Row -->
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-semibold text-gray-800">Duration</td>
                    @foreach($packages as $package)
                    <td class="px-6 py-4 text-center text-gray-800">
                        @if($package->durasi_bulan == 0)
                            Lifetime
                        @else
                            {{ $package->durasi_bulan }} months
                        @endif
                    </td>
                    @endforeach
                </tr>

                <!-- Features Row -->
                @if(count($packages) > 0 && $packages[0]->fitur)
                    @php
                        $allFeatures = [];
                        foreach($packages as $pkg) {
                            if($pkg->fitur) {
                                $allFeatures = array_unique(array_merge($allFeatures, $pkg->fitur));
                            }
                        }
                    @endphp

                    @foreach($allFeatures as $feature)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ ucfirst(str_replace('_', ' ', $feature)) }}</td>
                        @foreach($packages as $package)
                        <td class="px-6 py-4 text-center">
                            @if($package->fitur && in_array($feature, $package->fitur))
                                <i class="fas fa-check-circle text-green-500 text-lg"></i>
                            @else
                                <i class="fas fa-times-circle text-gray-300 text-lg"></i>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- FAQ Section -->
<div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 mt-8">
    <h3 class="font-bold text-blue-900 mb-4">Frequently Asked Questions</h3>
    <div class="space-y-4 text-sm text-blue-800">
        <div>
            <p class="font-semibold">What's the difference between packages?</p>
            <p class="mt-1">The main difference is the number of travel packages you can create, subscription duration, and premium features available.</p>
        </div>
        <div>
            <p class="font-semibold">Can I cancel my package?</p>
            <p class="mt-1">No, packages cannot be cancelled. However, you can upgrade to a better package anytime.</p>
        </div>
        <div>
            <p class="font-semibold">How is the payment process?</p>
            <p class="mt-1">Click the "Pay Now" button and you will be directed to Midtrans platform to make the payment.</p>
        </div>
    </div>
</div>

<!-- Back Button -->
<div class="mt-8 text-center">
    <a href="{{ route('travel-agent.subscriptions.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
        <i class="fas fa-arrow-left mr-2"></i> Back to Active Packages
    </a>
</div>
@endsection