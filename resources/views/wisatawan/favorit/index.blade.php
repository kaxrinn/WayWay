@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<section class="bg-white py-20 mt-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- HEADER -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#496d9e] mb-4">
                Destinasi Favorit
            </h1>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        @if($destinasiFavorit->count() > 0)
            {{-- Grid Destinasi - Style Sama dengan Index --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($destinasiFavorit as $item)
                <div class="relative bg-white rounded-[28px] shadow-md hover:shadow-xl
                            transition overflow-hidden group">
                    
                    {{-- Link ke Detail --}}
                    <a href="{{ route('destinasi.show', $item->id) }}" class="block">
                        {{-- Image --}}
                        <div class="h-[200px] relative overflow-hidden">
                            @if(is_array($item->foto) && count($item->foto) > 0)
                                <img src="{{ Storage::url($item->foto[0]) }}" 
                                     alt="{{ $item->nama_destinasi }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="p-5">
                            <h3 class="font-semibold text-gray-800 text-lg">
                                {{ $item->nama_destinasi }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                                {{ Str::limit($item->deskripsi, 80) }}
                            </p>

                            <div class="flex items-center justify-between mt-4">
                                <div class="text-[#496d9e] font-bold">
                                    Rp {{ number_format($item->harga, 0, ',', '.') }}
                                </div>
                                <span class="px-4 py-2 bg-[#496d9e] text-white text-sm rounded-full">
                                    Detail
                                </span>
                            </div>
                        </div>
                    </a>
                    
                    {{-- Favorit Button - Position Absolute --}}
                    <button onclick="event.preventDefault(); toggleFavorit({{ $item->id }})" 
                            id="btnFavorit{{ $item->id }}"
                            class="absolute top-3 right-3 w-9 h-9 bg-white/90 backdrop-blur-sm rounded-full 
                                   flex items-center justify-center hover:bg-white transition shadow-lg z-10">
                        <svg id="iconFavorit{{ $item->id }}" 
                             class="w-5 h-5 text-red-500 fill-red-500" 
                             fill="currentColor" 
                             stroke="currentColor" 
                             stroke-width="2"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" 
                                  stroke-linejoin="round" 
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $destinasiFavorit->links() }}
            </div>

        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-2xl shadow-md p-12 text-center">
                <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Destinasi Favorit</h3>
                <p class="text-gray-600 mb-6">
                    Anda belum menambahkan destinasi apapun ke favorit.<br>
                    Jelajahi destinasi wisata dan tambahkan yang Anda sukai!
                </p>
                <a href="{{ route('wisatawan.beranda') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-[#5b9ac7] hover:bg-[#496d9e] 
                          text-white font-medium rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Jelajahi Destinasi
                </a>
            </div>
        @endif

    </div>
</section>

{{-- SCRIPT FAVORIT TOGGLE --}}
<script>
function toggleFavorit(destinasiId) {
    const icon = document.getElementById('iconFavorit' + destinasiId);
    const btn = document.getElementById('btnFavorit' + destinasiId);
    
    // Disable button sementara
    btn.disabled = true;
    
    fetch('{{ route("wisatawan.favorit.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            destinasi_id: destinasiId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'removed') {
            // Hapus dari favorit - fade out card
            const card = btn.closest('.relative');
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                // Reload halaman untuk update grid
                window.location.reload();
            }, 300);
            
            showNotification(data.message, 'info');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan', 'error');
        btn.disabled = false;
    });
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-20 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full`;
    
    if (type === 'success') {
        notification.classList.add('bg-green-500');
    } else if (type === 'error') {
        notification.classList.add('bg-red-500');
    } else {
        notification.classList.add('bg-blue-500');
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>

@endsection