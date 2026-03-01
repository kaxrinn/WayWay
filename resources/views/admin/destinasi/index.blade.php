@extends('layouts.admin')

@section('title', 'Manage Destinations')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex flex-col sm:flex-row 
            sm:items-center sm:justify-between 
            gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-map-marked-alt text-primary"></i>
                Manage Destinations
            </h1>
            <p class="text-gray-500 mt-2">Manage tourist destinations available on the platform</p>
        </div>
        <a href="{{ route('admin.destinasi.create') }}" 
           class="bg-gradient-to-r from-green-500 to-green-600 
       hover:from-green-600 hover:to-green-700 
       text-white 
       w-full sm:w-auto
       text-sm sm:text-base
       px-4 sm:px-6 
       py-2 sm:py-3 
       rounded-lg 
       transition 
       shadow-lg hover:shadow-xl 
       transform hover:-translate-y-1 
       flex items-center justify-center gap-2"
            <i class="fas fa-plus"></i>
            Add Destination
        </a>
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

<!-- Search & Filter -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="relative">
            <input type="text" 
                   id="searchInput"
                   placeholder="🔍 Search destinations..."
                   class="w-full px-6 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition pl-12">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
        
        <select id="filterKategori" class="px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition">
            <option value="">All Categories</option>
            @foreach(\App\Models\Kategori::all() as $kat)
                <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Destinations Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="destinasiGrid">
    @forelse($destinasi as $destinasi)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-2 destinasi-card" 
         data-kategori="{{ $destinasi->kategori_id }}">
        <!-- Image -->
        <div class="relative h-48 bg-gray-200">
            @if($destinasi->foto)
                @php $foto = $destinasi->foto[0] ?? null; @endphp
                <img src="{{ $destinasi->foto[0] ? Storage::url($destinasi->foto[0]) : asset('placeholder.jpg') }}" 
                     alt="{{ $destinasi->nama_destinasi }}"
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary to-blue-400">
                    <i class="fas fa-image text-white text-6xl opacity-50"></i>
                </div>
            @endif
            
            <!-- Category Badge -->
            @if($destinasi->kategori)
            <div class="absolute top-3 right-3">
                <span class="bg-white/90 backdrop-blur-sm text-primary px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                    {{ $destinasi->kategori->nama_kategori }}
                </span>
            </div>
            @endif
        </div>
        
        <!-- Content -->
        <div class="p-5">
            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $destinasi->nama_destinasi }}</h3>
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
            </div>
            
            <!-- Actions -->
            <div class="flex gap-2 pt-3 border-t border-gray-200">
                <a href="{{ route('admin.destinasi.edit', $destinasi->id) }}" 
                   class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition text-sm font-medium text-center">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <form method="POST" 
                      action="{{ route('admin.destinasi.destroy', $destinasi->id) }}"
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
    @empty
    <div class="col-span-3 py-20 px-6 text-center">
        <div class="flex flex-col items-center justify-center text-gray-400">
            <i class="fas fa-map-marked-alt text-7xl mb-5"></i>
            <h3 class="text-2xl font-bold text-gray-600 mb-2">No Destinations Yet</h3>
            <p class="text-gray-500 mb-6">Click the "Add Destination" button to create a new destination</p>
            <a href="{{ route('admin.destinasi.create') }}" 
               class="bg-gradient-to-r from-primary to-blue-400 text-white px-6 py-3 rounded-lg transition shadow-lg hover:shadow-xl flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Add Destination
            </a>
        </div>
    </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const cards = document.querySelectorAll('.destinasi-card');
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(searchValue) ? '' : 'none';
    });
});

// Filter by category
document.getElementById('filterKategori').addEventListener('change', function() {
    const kategoriId = this.value;
    const cards = document.querySelectorAll('.destinasi-card');
    
    cards.forEach(card => {
        if (kategoriId === '' || card.dataset.kategori === kategoriId) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>
@endpush