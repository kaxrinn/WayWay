{{-- ===== SECTION: TRAVEL PACKAGES ===== --}}

<style>
.travel-dot {
    width: 10px;
    height: 10px;
    border-radius: 9999px;
    background: #d1d5db;
    cursor: pointer;
    transition: all 0.3s ease;
}
.travel-dot-active {
    width: 24px;
    background: #496d9e;
}
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-10 text-center">
            <span class="text-sm font-semibold text-[#496d9e] tracking-widest uppercase">
                Trusted Travel Agents
            </span>
            <h2 class="mt-2 text-3xl font-bold text-[#496d9e]">
                Featured Travel Packages
            </h2>
            <p class="mt-3 text-gray-500 max-w-xl mx-auto text-sm">
                Discover the best travel packages to the most popular destinations in the Riau Islands, carefully curated by trusted travel agencies.
            </p>
        </div>

        {{-- Slider Wrapper --}}
        <div class="relative">

            {{-- Arrow Left --}}
            <button id="travelScrollLeft"
                class="hidden lg:flex absolute -left-15 top-1/2 -translate-y-1/2 z-20
                       w-11 h-11 rounded-full bg-white shadow-lg items-center justify-center
                       text-gray-700 text-2xl font-light
                       hover:bg-[#496d9e] hover:text-white hover:shadow-xl transition-all">
                ‹
            </button>

            {{-- Arrow Right --}}
            <button id="travelScrollRight"
                class="hidden lg:flex absolute -right-15 top-1/2 -translate-y-1/2 z-20
                       w-11 h-11 rounded-full bg-white shadow-lg items-center justify-center
                       text-gray-700 text-2xl font-light
                       hover:bg-[#496d9e] hover:text-white hover:shadow-xl transition-all">
                ›
            </button>

            {{-- Track --}}
            <div class="overflow-x-auto lg:overflow-hidden scrollbar-hide px-4 sm:px-6 lg:px-0">
                <div id="travelTrack"
                     class="flex gap-4 sm:gap-5 lg:gap-6 transition-transform duration-500 ease-in-out">

                    @forelse ($travelPackages as $package)
                    <a href="{{ route('travel-packages.travel', $package->id) }}"
                       class="travel-card group flex-shrink-0 w-full sm:w-[300px] lg:w-[340px]
                              bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100
                              hover:shadow-lg transition-all duration-300 flex flex-col">

                        {{-- Thumbnail --}}
                        <div class="relative h-52 overflow-hidden bg-white">
                            @if ($package->thumbnail)
                                <img src="{{ Storage::url($package->thumbnail) }}"
                                     alt="{{ $package->nama_paket }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-100 to-cyan-200 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-[#496d9e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                                    </svg>
                                </div>
                            @endif

                            <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-gray-700 text-xs font-semibold px-2.5 py-1 rounded-full shadow-sm">
                                {{ $package->durasi_hari }} Days
                            </span>
                            <span class="absolute top-3 right-3 bg-[#496d9e]/90 backdrop-blur-sm text-white text-xs font-semibold px-2.5 py-1 rounded-full shadow-sm">
                                {{ \Carbon\Carbon::parse($package->tanggal_keberangkatan)->format('d M Y') }}
                            </span>
                        </div>

                        {{-- Content --}}
                        <div class="flex flex-col flex-1 p-5">

                            {{-- Travel Agent --}}
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-[#496d9e]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-gray-500 font-medium truncate">
                                    {{ $package->travelAgent->name ?? 'Travel Agent' }}
                                </span>
                            </div>

                            {{-- Package Name --}}
                            <h3 class="text-base font-bold text-gray-900 mb-1 group-hover:text-[#496d9e] transition-colors line-clamp-1">
                                {{ $package->nama_paket }}
                            </h3>

                            {{-- Destinations --}}
                            @if ($package->destinasi)
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach (array_slice($package->destinasi, 0, 3) as $dest)
                                <span class="text-xs bg-blue-50 text-[#496d9e] px-2 py-0.5 rounded-full">
                                    {{ $dest }}
                                </span>
                                @endforeach
                                @if (count($package->destinasi) > 3)
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                                    +{{ count($package->destinasi) - 3 }} more
                                </span>
                                @endif
                            </div>
                            @endif

                            {{-- Description --}}
                            <p class="text-sm text-gray-500 line-clamp-2 flex-1">
                                {{ $package->deskripsi }}
                            </p>

                            {{-- Footer --}}
                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-end justify-between">
                                <div>
                                    <p class="text-xs text-gray-400">Starting from</p>
                                    <p class="text-lg font-bold text-[#496d9e]">
                                        Rp {{ number_format($package->harga_per_orang, 0, ',', '.') }}
                                        <span class="text-xs font-normal text-gray-400">/person</span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-1 text-xs text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                                    </svg>
                                    {{ $package->min_peserta }}–{{ $package->max_peserta }} participants
                                </div>
                            </div>
                        </div>
                    </a>

                    @empty
                    <div class="w-full text-center py-16 text-gray-400">
                        <p>No travel packages are currently available.</p>
                    </div>
                    @endforelse

                </div>
            </div>
        </div>

        {{-- Dots --}}
        <div id="travelDots" class="flex justify-center gap-2 mt-6"></div>

        {{-- View All --}}
        @if ($travelPackages->count() >= 6)
        <div class="mt-8 text-center">
            <a href="{{ route('travel-packages.index') }}"
               class="inline-flex items-center gap-2 border border-[#496d9e] text-[#496d9e] hover:bg-[#496d9e] hover:text-white transition-colors px-6 py-2.5 rounded-full text-sm font-semibold">
                View All Packages
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        @endif

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let currentSlide = 0;
    let slidesToShow = 3;

    const track = document.getElementById('travelTrack');
    const cards = Array.from(document.querySelectorAll('.travel-card'));
    const prevBtn = document.getElementById('travelScrollLeft');
    const nextBtn = document.getElementById('travelScrollRight');
    const dotsContainer = document.getElementById('travelDots');

    function updateSlidesToShow() {
        if (window.innerWidth < 640) {
            slidesToShow = 1;
            track.parentElement.style.overflowX = 'auto';
        } else if (window.innerWidth < 1024) {
            slidesToShow = 2;
            track.parentElement.style.overflowX = 'auto';
        } else {
            slidesToShow = 3;
            track.parentElement.style.overflowX = 'hidden';
        }
    }

    function updateSlider() {
        if (!cards.length) return;
        const cardWidth = cards[0].offsetWidth + 24;
        track.style.transform = `translateX(-${currentSlide * cardWidth}px)`;
        [...dotsContainer.children].forEach((dot, i) => {
            dot.classList.toggle('travel-dot-active', i === currentSlide);
        });
    }

    function createDots() {
        dotsContainer.innerHTML = '';
        const maxSlide = Math.max(0, cards.length - slidesToShow);
        for (let i = 0; i <= maxSlide; i++) {
            const dot = document.createElement('div');
            dot.className = 'travel-dot' + (i === 0 ? ' travel-dot-active' : '');
            dot.onclick = () => { currentSlide = i; updateSlider(); };
            dotsContainer.appendChild(dot);
        }
    }

    prevBtn?.addEventListener('click', () => {
        currentSlide = Math.max(0, currentSlide - 1);
        updateSlider();
    });

    nextBtn?.addEventListener('click', () => {
        currentSlide = Math.min(cards.length - slidesToShow, currentSlide + 1);
        updateSlider();
    });

    window.addEventListener('resize', () => {
        updateSlidesToShow();
        currentSlide = 0;
        createDots();
        updateSlider();
    });

    updateSlidesToShow();
    createDots();
    updateSlider();
});
</script>