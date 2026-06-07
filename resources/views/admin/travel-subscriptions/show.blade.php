@extends('layouts.admin')

@section('title', 'Subscription Details')

@section('content')

<div class="grid grid-cols-3 gap-6 mb-6">
    <!-- Travel Agent Info -->
    <div class="col-span-2 bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                {{ strtoupper(substr($subscription->travelAgent->name, 0, 2)) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $subscription->travelAgent->name }}</h2>
                <p class="text-gray-500">{{ $subscription->travelAgent->email }}</p>
                <p class="text-gray-500">{{ $subscription->travelAgent->no_telepon }}</p>
            </div>
        </div>

```
    <div class="border-t pt-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Subscription Package</h3>
        <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white rounded-lg p-4">
            <p class="text-sm text-purple-100">Package</p>
            <p class="text-3xl font-bold">{{ $subscription->package->nama_paket }}</p>
            <p class="text-purple-100 mt-2">{{ $subscription->package->deskripsi }}</p>

            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-purple-100">Price</p>
                    <p class="text-xl font-bold">
                        @if($subscription->package->harga == 0)
                            FREE
                        @else
                            Rp {{ number_format($subscription->package->harga, 0, ',', '.') }}
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-sm text-purple-100">Max Packages</p>
                    <p class="text-xl font-bold">{{ $subscription->package->max_packages }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Card -->
<div class="bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Status</h3>

    <div class="space-y-4">

        <!-- Payment Status -->
        <div>
            <p class="text-gray-500 text-sm mb-1">Payment Method</p>
            <p class="text-lg font-bold">
                @if($subscription->payment_method === 'free')
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                        FREE
                    </span>
                @else
                    <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">
                        Midtrans
                    </span>
                @endif
            </p>
        </div>

        <!-- Subscription Status -->
        <div>
            <p class="text-gray-500 text-sm mb-1">Subscription Status</p>
            <p>
                @if($subscription->status === 'active')
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold">
                        <i class="fas fa-check-circle mr-1"></i> Active
                    </span>
                @elseif($subscription->status === 'pending')
                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-bold">
                        <i class="fas fa-clock mr-1"></i> Pending
                    </span>
                @else
                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-bold">
                        <i class="fas fa-times-circle mr-1"></i> Expired
                    </span>
                @endif
            </p>
        </div>

        <!-- Start Date -->
        <div>
            <p class="text-gray-500 text-sm mb-1">Start Date</p>
            <p class="font-bold text-gray-800">
                {{ $subscription->started_at->format('d M Y H:i') }}
            </p>
        </div>

        <!-- Expiration Date -->
        <div>
            <p class="text-gray-500 text-sm mb-1">Expiration Date</p>
            <p class="font-bold">
                @if($subscription->expired_at === null)
                    <span class="text-green-600">Lifetime</span>
                @else
                    {{ $subscription->expired_at->format('d M Y H:i') }}

                    @if($subscription->expired_at < now())
                        <span class="text-red-500 text-xs">(Expired)</span>
                    @elseif($subscription->expired_at < now()->addDays(7))
                        <span class="text-yellow-500 text-xs">(Expiring Soon)</span>
                    @endif
                @endif
            </p>
        </div>

        <!-- Actions -->
        @if($subscription->status === 'pending')
        <div class="border-t pt-4 space-y-2">

            <form method="POST" action="{{ route('admin.travel-subscriptions.approve', $subscription->id) }}">
                @csrf
                <button type="submit"
                        class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded font-bold transition">
                    <i class="fas fa-check-circle mr-2"></i>
                    Approve Subscription
                </button>
            </form>

            <form method="POST" action="{{ route('admin.travel-subscriptions.reject', $subscription->id) }}">
                @csrf
                <button type="submit"
                        class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded font-bold transition">
                    <i class="fas fa-ban mr-2"></i>
                    Reject Subscription
                </button>
            </form>

        </div>
        @endif

    </div>
</div>
```

</div>

<!-- Features -->

<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Package Features</h3>

```
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">

    @if($subscription->package->fitur)

        @foreach($subscription->package->fitur as $fitur)
        <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded">
            <p class="text-green-800 text-sm font-semibold">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                {{ ucfirst(str_replace('_', ' ', $fitur)) }}
            </p>
        </div>
        @endforeach

    @else

        <p class="text-gray-500">
            No Special Features Available
        </p>

    @endif

</div>
```

</div>

<!-- Snap Token (if available) -->

@if($subscription->snap_token)

<div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
    <p class="font-bold text-blue-900 mb-2">
        Midtrans Snap Token
    </p>

```
<code class="bg-white p-2 rounded text-xs block overflow-auto">
    {{ $subscription->snap_token }}
</code>
```

</div>
@endif

@endsection
