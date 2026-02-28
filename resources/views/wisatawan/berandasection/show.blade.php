@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen mt-19">

    {{-- HEADER --}}
    <div class="bg-white shadow-sm top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 py-1">
            <div class="flex items-center justify-between">
                <a href="javascript:history.back()"
                   class="flex items-center gap-2 text-gray-600 hover:text-[#496d9e]">
                    <span class="font-medium">Back</span>
                </a>

                {{-- FAVORITE BUTTON --}}
                @auth
                    <button onclick="toggleFavorit({{ $destinasi->id }})"
                            id="btnFavorit"
                            class="p-2 hover:bg-red-50 rounded-full transition group">
                        <svg id="iconFavorit"
                             class="w-6 h-6 transition {{ $isFavorited ? 'text-red-500 fill-red-500' : 'text-gray-400' }}"
                             fill="{{ $isFavorited ? 'currentColor' : 'none' }}"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                @else
                    <a href="{{ route('login') }}"
                       class="p-2 hover:bg-red-50 rounded-full transition group">
                        <svg class="w-6 h-6 text-gray-400 group-hover:text-red-500 transition"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-2">

        {{-- MEDIA GALLERY --}}
        @php
            $media = [];
            if (is_array($destinasi->foto)) {
                foreach ($destinasi->foto as $f) {
                    $media[] = ['type' => 'image', 'path' => $f];
                }
            }
            if (is_array($destinasi->video)) {
                foreach ($destinasi->video as $v) {
                    $media[] = ['type' => 'video', 'path' => $v];
                }
            }
        @endphp

        @if(count($media))
        <div class="relative mb-6">
            <div class="relative h-[400px] rounded-3xl overflow-hidden shadow-lg group">
                @foreach($media as $i => $m)
                <div class="media-slide {{ $i === 0 ? 'block' : 'hidden' }} absolute inset-0">
                    @if($m['type'] === 'image')
                        <img src="{{ Storage::url($m['path']) }}"
                             class="w-full h-full object-cover"
                             alt="{{ $destinasi->nama_destinasi }}">
                    @else
                        <video controls class="w-full h-full object-cover bg-black">
                            <source src="{{ Storage::url($m['path']) }}" type="video/mp4">
                        </video>
                    @endif
                </div>
                @endforeach

                {{-- Navigation --}}
                @if(count($media) > 1)
                <button onclick="prevMedia()"
                        class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10
                               bg-white/90 hover:bg-white rounded-full shadow-lg
                               flex items-center justify-center transition opacity-0 group-hover:opacity-100">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button onclick="nextMedia()"
                        class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10
                               bg-white/90 hover:bg-white rounded-full shadow-lg
                               flex items-center justify-center transition opacity-0 group-hover:opacity-100">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                {{-- Indicator Dots --}}
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                    @foreach($media as $i => $m)
                    <button onclick="goMedia({{ $i }})"
                            class="indicator w-2 h-2 rounded-full transition
                                   {{ $i === 0 ? 'bg-white w-6' : 'bg-white/60' }}">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- OVERVIEW --}}
        <div class="bg-white rounded-3xl shadow-md p-6 lg:p-8 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-4">
                <div class="flex-1">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3">
                        {{ $destinasi->nama_destinasi }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-3">
                        @if($destinasi->kategori)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#F4DBB4] text-gray-700 rounded-full text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            {{ $destinasi->kategori->nama_kategori }}
                        </span>
                        @endif

                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#5b9ac7] text-white rounded-full text-sm font-medium">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            {{ number_format($avgRating ?? 5, 1) }}
                        </span>

                        <span class="text-sm text-gray-500">
                            ({{ $destinasi->ulasan->count() }} reviews)
                        </span>
                    </div>
                </div>

                {{-- PRICE --}}
                <div class="lg:text-right">
                    <div class="text-sm text-gray-500 mb-1">Starting from</div>
                    <div class="text-3xl lg:text-4xl font-bold text-[#496d9e]">
                        Rp {{ number_format($destinasi->harga, 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-gray-500">/day</div>
                </div>
            </div>

            @if($destinasi->kategori && $destinasi->kategori->deskripsi_kategori)
            <div class="mb-4 p-4 bg-blue-50 rounded-2xl">
                <p class="text-sm text-gray-600">
                    {{ $destinasi->kategori->deskripsi_kategori }}
                </p>
            </div>
            @endif

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ $destinasi->deskripsi }}
                </p>
            </div>
        </div>

        {{-- LOCATION --}}
        <div class="bg-white rounded-3xl shadow-md p-6 lg:p-8 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-[#496d9e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Location
            </h2>

            <div class="rounded-2xl overflow-hidden shadow-lg">
                <iframe
                    class="w-full h-[300px]"
                    src="https://maps.google.com/maps?q={{ $destinasi->latitude }},{{ $destinasi->longitude }}&output=embed"
                    loading="lazy">
                </iframe>
            </div>
        </div>

        {{-- REVIEWS --}}
        <div class="bg-white rounded-3xl shadow-md p-6 lg:p-8 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#496d9e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    Visitor Reviews
                </h2>

                @if($destinasi->ulasan->count())
                <div class="text-right">
                    <div class="text-2xl font-bold text-[#496d9e]">{{ number_format($avgRating ?? 5, 1) }}</div>
                    <div class="text-xs text-gray-500">{{ $destinasi->ulasan->count() }} reviews</div>
                </div>
                @endif
            </div>

            @if($destinasi->ulasan->count())
            {{-- Horizontal Scroll Reviews --}}
            <div class="overflow-x-auto pb-4 -mx-6 px-6 mb-6">
                <div class="flex gap-4 min-w-max">
                    @foreach($destinasi->ulasan as $u)
                    <div class="w-72 p-4 bg-gray-50 rounded-2xl flex-shrink-0">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-[#496d9e] rounded-full flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr($u->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-900 truncate">{{ $u->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $u->created_at->format('d M Y') }}</div>
                            </div>
                            <div class="flex items-center gap-1 px-2 py-1 bg-[#F4DBB4] rounded-full flex-shrink-0">
                                <svg class="w-3 h-3 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="text-xs font-semibold">{{ $u->rating }}</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 line-clamp-3">{{ $u->komentar }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="text-center py-8 mb-6">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <p class="text-gray-400">No reviews yet</p>
            </div>
            @endif

            {{-- REVIEW FORM --}}
            @auth
                <h3 class="font-semibold text-gray-900 mb-4">Write a Review</h3>

                <form action="{{ route('ulasan.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="destinasi_id" value="{{ $destinasi->id }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <div class="flex gap-2">
                            @for($i=1; $i<=5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" name="rating" value="{{ $i }}" class="sr-only peer" {{ $i === 5 ? 'checked' : '' }}>
                                <div class="w-10 h-10 flex items-center justify-center rounded-xl border-2 border-gray-300
                                            peer-checked:border-[#5b9ac7] peer-checked:bg-[#5b9ac7] peer-checked:text-white
                                            hover:border-[#5b9ac7] transition">
                                    <span class="font-semibold text-sm">{{ $i }}</span>
                                </div>
                            </label>
                            @endfor
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                        <textarea name="komentar" rows="3" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl
                                         focus:ring-2 focus:ring-[#5b9ac7] focus:border-transparent
                                         transition resize-none"
                                  placeholder="Share your experience..."></textarea>
                    </div>

                    <button type="submit"
                            class="w-full py-3 bg-[#F4DBB4] hover:bg-[#f9d497]
                                   text-gray-800 font-semibold rounded-xl transition">
                        Submit Review
                    </button>
                </form>
            @else
            <div class="pt-6 border-t border-gray-200 text-center">
                <p class="text-gray-600 mb-3 text-sm">Login to leave a review</p>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 px-6 py-2 bg-[#5b9ac7] hover:bg-[#496d9e]
                          text-white font-medium rounded-xl transition text-sm">
                    Login Now
                </a>
            </div>
            @endauth
        </div>

        <div class="flex justify-center mt-7 mb-8">
            <a href="{{ route('wisatawan.beranda') }}"
               class="bg-[#5b9ac7] hover:bg-[#496d9e]
                      text-white font-medium
                      rounded-full px-6 py-2 text-sm transition">
                Back to Home
            </a>
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
<script>
// ======================================
// MEDIA GALLERY SLIDER
// ======================================
let currentIndex = 0;
const slides     = document.querySelectorAll('.media-slide');
const indicators = document.querySelectorAll('.indicator');

function showMedia(index) {
    if (index < 0) index = slides.length - 1;
    if (index >= slides.length) index = 0;

    slides.forEach((slide, i) => {
        slide.classList.toggle('hidden', i !== index);
    });

    indicators.forEach((ind, i) => {
        if (i === index) {
            ind.classList.remove('bg-white/60', 'w-2');
            ind.classList.add('bg-white', 'w-6');
        } else {
            ind.classList.remove('bg-white', 'w-6');
            ind.classList.add('bg-white/60', 'w-2');
        }
    });

    currentIndex = index;
}

function nextMedia() { showMedia(currentIndex + 1); }
function prevMedia() { showMedia(currentIndex - 1); }
function goMedia(index) { showMedia(index); }

document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft')  prevMedia();
    if (e.key === 'ArrowRight') nextMedia();
});

// ======================================
// FAVORITE TOGGLE
// ======================================
function toggleFavorit(destinasiId) {
    const icon = document.getElementById('iconFavorit');
    const btn  = document.getElementById('btnFavorit');

    btn.disabled = true;

    fetch('{{ route("wisatawan.favorit.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ destinasi_id: destinasiId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'added') {
            icon.classList.remove('text-gray-400');
            icon.classList.add('text-red-500', 'fill-red-500');
            icon.setAttribute('fill', 'currentColor');
            showNotification('Added to favorites', 'success');
        } else if (data.status === 'removed') {
            icon.classList.remove('text-red-500', 'fill-red-500');
            icon.classList.add('text-gray-400');
            icon.setAttribute('fill', 'none');
            showNotification('Removed from favorites', 'info');
        }
    })
    .catch(() => showNotification('Something went wrong', 'error'))
    .finally(() => { btn.disabled = false; });
}

// ======================================
// NOTIFICATION HELPER
// ======================================
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white
                               transform transition-all duration-300 translate-x-full`;

    if (type === 'success')     notification.classList.add('bg-green-500');
    else if (type === 'error')  notification.classList.add('bg-red-500');
    else                        notification.classList.add('bg-blue-500');

    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => notification.classList.remove('translate-x-full'), 100);
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => document.body.removeChild(notification), 300);
    }, 3000);
}
</script>
@endsection