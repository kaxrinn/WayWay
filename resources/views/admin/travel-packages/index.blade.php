@extends('layouts.admin')

@section('title', 'Travel Agent Packages')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-box text-purple-500"></i>
            Travel Agent Packages
        </h1>
        <p class="text-gray-500 mt-2">Manage subscription packages for travel agents</p>
    </div>
    
    <a href="{{ route('admin.travel-subscriptions.packages.create') }}" 
       class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition flex items-center gap-2 font-semibold">
        <i class="fas fa-plus"></i>
        Add Package
    </a>
</div>

<!-- Success Alert -->
@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow">
    <i class="fas fa-check-circle mr-2"></i>
    {{ session('success') }}
</div>
@endif

<!-- Packages Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">No</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Package</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Price</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Max Packages</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Duration</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Subscribers</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($packages as $key => $package)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-semibold text-gray-600">{{ $key + 1 }}</td>
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $package->nama_paket }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-primary">
                        @if($package->harga == 0)
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">
                                <i class="fas fa-gift mr-1"></i> FREE
                            </span>
                        @else
                            Rp {{ number_format($package->harga, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-bold">
                            {{ $package->max_packages }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-sm">
                        @if($package->durasi_bulan == 0)
                            <span class="text-gray-600">Lifetime</span>
                        @else
                            {{ $package->durasi_bulan }} bulan
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-bold">
                            {{ $package->subscriptions_count ?? 0 }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($package->status == 'active')
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">
                                <i class="fas fa-check-circle mr-1"></i> Active
                            </span>
                        @else
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-bold">
                                <i class="fas fa-times-circle mr-1"></i> Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex gap-2 justify-center">
                            <a href="{{ route('admin.travel-subscriptions.packages.edit', $package->id) }}" 
                               class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-semibold">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.travel-subscriptions.packages.toggle-status', $package->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs font-semibold">
                                    <i class="fas fa-exchange-alt mr-1"></i> Toggle
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.travel-subscriptions.packages.destroy', $package->id) }}" 
                                  onsubmit="return confirm('Hapus paket ini?')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-inbox text-6xl mb-4"></i>
                        <p class="text-lg font-medium">not have package</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection