@extends('layouts.travel-agent')

@section('title', 'My Tour Packages')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-suitcase-rolling text-blue-500"></i>
            My Tour Packages
        </h1>
        <p class="text-gray-500 mt-2">Manage all your travel packages</p>
    </div>
    
    @if($currentPackages < $maxPackages)
    <a href="{{ route('travel-agent.packages.create') }}" 
       class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition flex items-center gap-2 font-semibold">
        <i class="fas fa-plus"></i>
        Add Package
    </a>
    @else
    <div class="bg-red-100 border-2 border-red-300 rounded-lg px-6 py-3">
        <p class="text-red-800 font-semibold">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            You have reached your limit!
        </p>
        <a href="{{ route('travel-agent.subscriptions.upgrade') }}" class="text-red-600 hover:text-red-800 underline text-sm mt-1">
            Upgrade your plan to add more packages
        </a>
    </div>
    @endif
</div>

<!-- Quota Info -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg text-white p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm">Active Packages</p>
                <p class="text-4xl font-bold">{{ $currentPackages }}/{{ $maxPackages }}</p>
            </div>
            <i class="fas fa-chart-pie text-6xl opacity-30"></i>
        </div>
    </div>

    <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl shadow-lg text-white p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm">Package Status</p>
                <p class="text-2xl font-bold">
                    @if($currentPackages == 0)
                        No packages yet
                    @elseif($currentPackages < $maxPackages)
                        Active ({{ $maxPackages - $currentPackages }} slots remaining)
                    @else
                        Full (Upgrade for more)
                    @endif
                </p>
            </div>
            <i class="fas fa-flag text-6xl opacity-30"></i>
        </div>
    </div>
</div>

<!-- Success Alert -->
@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow">
    <i class="fas fa-check-circle mr-2"></i>
    {{ session('success') }}
</div>
@endif

<!-- Packages Cards -->
@if($packages->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    @foreach($packages as $package)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition">
        <!-- Thumbnail -->
        @if($package->thumbnail)
        <img src="{{ asset('storage/' . $package->thumbnail) }}" alt="{{ $package->nama_paket }}" 
             class="w-full h-48 object-cover">
        @else
        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
            <i class="fas fa-image text-gray-400 text-4xl"></i>
        </div>
        @endif

        <!-- Content -->
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $package->nama_paket }}</h3>
            
            <!-- Details -->
            <div class="space-y-2 mb-4">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-calendar text-blue-500 mr-2 w-4"></i>
                    {{ $package->durasi_hari }} days
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-clock text-orange-500 mr-2 w-4"></i>
                    Departure: {{ $package->tanggal_keberangkatan->format('d M Y') }}
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-users text-green-500 mr-2 w-4"></i>
                    {{ $package->min_peserta }}-{{ $package->max_peserta }} participants
                </div>
            </div>

            <!-- Price -->
            <div class="bg-blue-50 rounded-lg p-3 mb-4">
                <p class="text-sm text-gray-600">Price per person</p>
                <p class="text-2xl font-bold text-blue-600">
                    Rp {{ number_format($package->harga_per_orang, 0, ',', '.') }}
                </p>
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
                <a href="{{ route('travel-agent.packages.edit', $package->id) }}" 
                   class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold text-center transition">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <form method="POST" action="{{ route('travel-agent.packages.destroy', $package->id) }}" 
                      onsubmit="return confirm('Delete this package?')" style="flex: 1;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold transition">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination -->
<div class="mt-8">
    {{ $packages->links() }}
</div>

@else
<!-- Empty State -->
<div class="bg-white rounded-xl shadow-lg p-12 text-center">
    <i class="fas fa-inbox text-8xl text-gray-300 mb-4"></i>
    <p class="text-2xl font-bold text-gray-600 mb-2">No tour packages yet</p>
    <p class="text-gray-500 mb-6">Start creating your first travel package now</p>
    
    <a href="{{ route('travel-agent.packages.create') }}" 
       class="inline-block bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:shadow-lg transition">
        <i class="fas fa-plus mr-2"></i> Create First Package
    </a>
</div>
@endif
@endsection