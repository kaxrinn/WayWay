@extends('layouts.pemilik')

@section('title', 'My Destinations')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-map-marked-alt text-primary"></i>
                My Tourist Destinations
            </h1>
            <p class="text-gray-500 mt-2">Manage all your tourist destinations</p>
        </div>
        @if($destinasi->count() < ($limits['max_destinasi'] ?? PHP_INT_MAX))
        <a href="{{ route('pemilik.destinasi.create') }}" 
           class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-lg transition shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center gap-2 justify-center">
            <i class="fas fa-plus"></i>
            Add Destination
        </a>
        @else
        <div class="text-center">
            <p class="text-red-600 font-semibold mb-2">Destination limit reached!</p>
            <a href="{{ route('pemilik.paket.index') }}" 
               class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-3 rounded-lg transition shadow-lg hover:shadow-xl inline-flex items-center gap-2">
                <i class="fas fa-rocket"></i>
                Upgrade Plan
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Success Alert -->
@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow">
    <div class="flex items-center">
        <i class="fas fa-check-circle text-2xl mr-3"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
</div>
@endif

<!-- Error Alert -->
@if(session('error'))
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 shadow">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
</div>
@endif

<!-- Stats Card -->
<div class="bg-gradient-to-r from-primary to-blue-400 rounded-xl p-6 mb-6 text-white shadow-lg">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="flex items-center gap-4">
            <div class="bg-white/20 p-4 rounded-full">
                <i class="fas fa-map-marked-alt text-3xl"></i>
            </div>
            <div>
                <p class="text-sm text-white/80">Total Destinations</p>
                <p class="text-3xl font-bold">{{ $destinasi->count() }} / {{ $limits['max_destinasi'] ?? '∞' }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="bg-white/20 p-4 rounded-full">
                <i class="fas fa-images text-3xl"></i>
            </div>
            <div>
                <p class="text-sm text-white/80">Photo Limit / Destination</p>
                <p class="text-3xl font-bold">{{ $limits['max_foto'] ?? '∞' }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="bg-white/20 p-4 rounded-full">
                <i class="fas fa-video text-3xl"></i>
            </div>
            <div>
                <p class="text-sm text-white/80">Video Limit / Destination</p>
                <p class="text-3xl font-bold">{{ $limits['max_video'] ?? '∞' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Destination Grid -->
@if($destinasi->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($destinasi as $destinasi)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-2">
        <!-- Image -->
        <div class="relative h-48 bg-gray-200">
            @if($destinasi->foto && count($destinasi->foto) > 0)
                <img src="{{ Storage::url($destinasi->foto[0]) }}" 
                     alt="{{ $destinasi->nama_destinasi }}"
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary to-blue-400">
                    <i class="fas fa-image text-white text-6xl opacity-50"></i>
                </div>
            @endif
            
            <!-- Featured Badge -->
            @if($destinasi->is_featured)
            <div class="absolute top-3 left-3">
                <span class="bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold shadow-lg flex items-center gap-1">
                    <i class="fas fa-star"></i>
                    Featured
                </span>
            </div>
            @endif
            
            <!-- Status Badge -->
            <div class="absolute top-3 right-3">
                @if($destinasi->status == 'active')
                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                        <i class="fas fa-check-circle"></i>
                        Active
                    </span>
                @else
                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                        <i class="fas fa-times-circle"></i>
                        Inactive
                    </span>
                @endif
            </div>
        </div>
        
        <!-- Content -->
        <div class="p-5">
            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $destinasi->nama_destinasi }}</h3>
            
            <!-- Category -->
            @if($destinasi->kategori)
            <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium mb-3">
                {{ $destinasi->kategori->nama_kategori }}
            </span>
            @endif
            
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($destinasi->deskripsi, 100) }}</p>
            
            <!-- Info -->
            <div class="space-y-2 mb-4">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-map-marker-alt w-5 text-red-500"></i>
                    <span>{{ $destinasi->latitude }}, {{ $destinasi->longitude }}</span>
                </div>
                <div class="flex items-center text-sm font-bold text-primary">
                    <i class="fas fa-tag w-5"></i>
                    <span>Rp {{ number_format($destinasi->harga, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-images w-5 text-green-500"></i>
                    <span>{{ count($destinasi->foto ?? []) }} photos, {{ count($destinasi->video ?? []) }} videos</span>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-2 pt-3 border-t border-gray-200 flex-col sm:flex-row">
                @php
                    $canEdit = $limits['can_edit_foto'] ?? false;
                    
                    if (!$canEdit) {
                        try {
                            $hasApprovedRequest = \App\Models\EditRequest::where('user_id', auth()->id())
                                ->where('destinasi_id', $destinasi->id)
                                ->where('status', 'approved')
                                ->where('approved_at', '>=', now()->subDays(7))
                                ->exists();
                            
                            $canEdit = $hasApprovedRequest;
                        } catch (\Exception $e) {
                            $canEdit = false;
                        }
                    }
                @endphp
                
                @if($canEdit)
                <a href="{{ route('pemilik.destinasi.edit', $destinasi->id) }}" 
                   class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition text-sm font-medium text-center">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                @else
                <a href="{{ route('pemilik.edit-request.create', ['destinasi_id' => $destinasi->id]) }}" 
                   class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition text-sm font-medium text-center">
                    <i class="fas fa-paper-plane mr-1"></i> Request Edit
                </a>
                @endif
                
                <form method="POST" 
                      action="{{ route('pemilik.destinasi.destroy', $destinasi->id) }}"
                      onsubmit="return confirm('Are you sure you want to delete this destination?')"
                      class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition text-sm font-medium">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Info Edit Permission -->
@if(!$limits['can_edit_foto'])
<div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
        <div>
            <p class="font-semibold text-blue-800 mb-1">Basic Plan - Limited Edit Access</p>
            <p class="text-sm text-blue-700">
                To edit a destination, you must submit a request to admin. 
                <a href="{{ route('pemilik.paket.index') }}" class="underline font-semibold">Upgrade to Standard/Premium</a> 
                to edit directly without approval.
            </p>
        </div>
    </div>
</div>
@endif

@else
<!-- Empty State -->
<div class="bg-white rounded-xl shadow-lg py-20 px-6 text-center">
    <div class="flex flex-col items-center justify-center text-gray-400">
        <i class="fas fa-map-marked-alt text-8xl mb-5"></i>
        <h3 class="text-2xl font-bold text-gray-600 mb-2">No Destinations Yet</h3>
        <p class="text-gray-500 mb-6">Start by adding your first tourist destination!</p>
        
        @if($destinasi->count() < ($limits['max_destinasi'] ?? PHP_INT_MAX))
        <a href="{{ route('pemilik.destinasi.create') }}" 
           class="bg-gradient-to-r from-primary to-blue-400 text-white px-8 py-4 rounded-lg transition shadow-lg hover:shadow-xl flex items-center gap-2 font-semibold">
            <i class="fas fa-plus"></i>
            Add First Destination
        </a>
        @endif
    </div>
</div>
@endif
@endsection