@extends('layouts.admin')

@section('title', 'Detail Travel Agent')

@section('content')

<div class="grid grid-cols-3 gap-6 mb-6">
    <!-- Agent Info -->
    <div class="col-span-2 bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-3xl font-bold">
                {{ strtoupper(substr($travelAgent->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $travelAgent->name }}</h2>
                <p class="text-gray-500">{{ $travelAgent->email }}</p>
                <p class="text-gray-500">{{ $travelAgent->no_telepon }}</p>
                <p class="text-xs text-gray-400 mt-1">Bergabung: {{ $travelAgent->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Statistik</h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-gray-500 text-sm">Total Paket</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_packages'] }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-gray-500 text-sm">Active Package</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['active_packages'] }}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <p class="text-gray-500 text-sm">Total Transactions</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['total_transactions'] }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="border-t pt-6 mt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Actions</h3>
            <div class="flex gap-3">
                <a href="{{ route('admin.travel-agents.edit', $travelAgent->id) }}" 
                   class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-3 rounded-lg font-bold transition text-center">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <form method="POST" action="{{ route('admin.travel-agents.destroy', $travelAgent->id) }}" 
                      onsubmit="return confirm('Sure to delete this travel agent?')" style="flex: 1;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-lg font-bold transition">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Subscription Info -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Active Subscription</h3>
        
        @if($activeSubscription)
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg p-4">
                <p class="text-sm text-purple-100">Current Package</p>
                <p class="text-2xl font-bold">{{ $activeSubscription->package->nama_paket }}</p>
                <p class="text-purple-100 text-xs mt-2">Rp {{ number_format($activeSubscription->package->harga, 0, ',', '.') }}</p>
                
                <div class="border-t border-purple-400 mt-4 pt-4">
                    <p class="text-sm text-purple-100">Max Travel Packages</p>
                    <p class="text-2xl font-bold">{{ $activeSubscription->package->max_packages }}</p>
                </div>

                <div class="border-t border-purple-400 mt-4 pt-4">
                    <p class="text-sm text-purple-100">Status</p>
                    <p class="text-lg font-bold">
                        @if($activeSubscription->status === 'active')
                            ✓ Aktif
                        @else
                            ✗ {{ $activeSubscription->status }}
                        @endif
                    </p>
                </div>

                <div class="border-t border-purple-400 mt-4 pt-4">
                    <p class="text-sm text-purple-100">Expired</p>
                    @if($activeSubscription->expired_at === null)
                        <p class="text-lg font-bold">Lifetime</p>
                    @else
                        <p class="text-lg font-bold">{{ $activeSubscription->expired_at->format('d M Y') }}</p>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                <i class="fas fa-exclamation-circle text-gray-400 text-3xl mb-2"></i>
                <p class="text-gray-600 font-semibold">No active subscription</p>
                <p class="text-gray-500 text-sm mt-1">Travel agent has not purchased a package</p>
            </div>
        @endif
    </div>
</div>

<!-- Subscription History -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b">
        <h3 class="text-lg font-bold text-gray-800">
            <i class="fas fa-history text-blue-500 mr-2"></i>
            Subscription History
        </h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Package</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Start</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">End</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($subscriptions as $sub)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $sub->package->nama_paket }}</td>
                    <td class="px-6 py-4">
                        @if($sub->package->harga == 0)
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">FREE</span>
                        @else
                            Rp {{ number_format($sub->package->harga, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($sub->status === 'active')
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">✓ Aktif</span>
                        @elseif($sub->status === 'pending')
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold">⏳ Pending</span>
                        @else
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-bold">✗ Expired</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $sub->started_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($sub->expired_at === null)
                            <span class="text-green-600 font-semibold">Lifetime</span>
                        @else
                            {{ $sub->expired_at->format('d M Y') }}
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p class="text-sm">No subscription history available</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 bg-gray-50 border-t">
        {{ $subscriptions->links() }}
    </div>
</div>
@endsection