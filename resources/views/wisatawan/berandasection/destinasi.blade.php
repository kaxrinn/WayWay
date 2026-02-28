<style>
.filter-item {
    padding: 6px 16px;
    border-radius: 9999px;
    color: #6B7280;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
    flex-shrink: 0;
    transition: background 0.2s, color 0.2s;
}
.filter-item:hover {
    background: #f9d497;
}
.filter-active {
    background: rgb(244, 219, 180);
    color: #4E4E4E;
}
.filter-fade-right {
    background: linear-gradient(to right, transparent, white 80%);
}
.filter-fade-left {
    background: linear-gradient(to left, transparent, white 80%);
}
.dot {
    width: 8px;
    height: 8px;
    border-radius: 9999px;
    background: #d1d5db;
    cursor: pointer;
    transition: all 0.3s ease;
    flex-shrink: 0;
}
.dot-active {
    width: 24px;
    background: #496d9e;
}
.dot-hidden {
    display: none;
}
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<section id="destinasi" class="bg-white py-20">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8 sm:px-5">

        <!-- LEFT: TITLE + FILTER -->
        <div class="flex flex-col gap-4 min-w-0 flex-1">
            <h2 class="text-3xl font-bold text-[#496d9e]">
                Popular Destinations
            </h2>

            <!-- FILTER SCROLL WRAPPER -->
            <div class="relative">
                <div id="filterFadeLeft"
                     class="filter-fade-left pointer-events-none absolute left-0 top-0 h-full w-10 z-10 hidden">
                </div>

                <div id="filterScroll"
                     class="flex gap-2 text-sm overflow-x-auto scrollbar-hide pb-1">

                    <button class="filter-item filter-active" data-kategori="all">
                        Popular
                    </button>

                    @foreach($kategori as $kat)
                        @if($destinasiPopuler->where('kategori_id', $kat->id)->count() > 0)
                        <button class="filter-item" data-kategori="{{ $kat->id }}">
                            {{ $kat->nama_kategori }}
                        </button>
                        @endif
                    @endforeach
                </div>

                <div id="filterFadeRight"
                     class="filter-fade-right pointer-events-none absolute right-0 top-0 h-full w-10 z-10">
                </div>
            </div>
        </div>

        <!-- RIGHT: SEE ALL -->
        <div class="self-start sm:self-center sm:ml-4 flex-shrink-0">
            <a href="{{ route('destinasi.index') }}"
               class="inline-flex items-center gap-2
                      bg-[#5b9ac7] hover:bg-[#496d9e]
                      text-white font-medium
                      rounded-full px-6 py-2 text-sm transition">
                See All
                <span>→</span>
            </a>
        </div>
    </div>

    <!-- SLIDER WRAPPER -->
    <div class="relative">

        <button id="scrollLeft"
            class="hidden lg:flex absolute -left-15 top-1/2 -translate-y-1/2 z-20
                   w-11 h-11 rounded-full bg-white shadow-lg items-center justify-center
                   text-gray-700 text-2xl font-light
                   hover:bg-[#496d9e] hover:text-white hover:shadow-xl transition-all">
            ‹
        </button>

        <button id="scrollRight"
            class="hidden lg:flex absolute -right-15 top-1/2 -translate-y-1/2 z-20
                   w-11 h-11 rounded-full bg-white shadow-lg items-center justify-center
                   text-gray-700 text-2xl font-light
                   hover:bg-[#496d9e] hover:text-white hover:shadow-xl transition-all">
            ›
        </button>

        <div id="destinasiWrapper" class="overflow-x-auto lg:overflow-hidden scrollbar-hide px-4 sm:px-6 lg:px-0">
            <div id="destinasiTrack"
                 class="flex gap-4 sm:gap-5 lg:gap-6 transition-transform duration-500 ease-in-out">

                @foreach ($destinasiPopuler as $destinasi)
                <a href="{{ route('destinasi.show', $destinasi->id) }}"
                   data-kategori="{{ $destinasi->kategori_id }}"
                   class="destinasi-card flex-shrink-0 w-full sm:w-[300px] lg:w-[340px]
                          bg-white rounded-[24px]
                          shadow-[0_2px_16px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_24px_rgba(0,0,0,0.15)]
                          transition-all duration-300 overflow-hidden group">

                    <div class="relative h-[200px] sm:h-[180px] overflow-hidden">
                        <img
                            src="{{ $destinasi->foto ? Storage::url($destinasi->foto[0]) : asset('images/placeholder.jpg') }}"
                            alt="{{ $destinasi->nama_destinasi }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500 rounded-t-[24px]">
                    </div>

                    <div class="p-4 sm:p-5">
                        <h3 class="font-bold text-gray-900 text-base sm:text-lg mb-1.5 leading-tight">
                            {{ $destinasi->nama_destinasi }}
                        </h3>

                        <p class="text-xs sm:text-sm text-gray-500 leading-relaxed mb-4 line-clamp-2 min-h-[36px]">
                            {{ Str::limit($destinasi->deskripsi, 80) }}
                        </p>

                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <div class="flex flex-col">
                                <span class="text-[10px] sm:text-xs text-gray-400 font-medium">
                                    {{ $destinasi->kategori->nama_kategori ?? 'Travel Package' }}
                                </span>
                                <span class="text-[10px] text-gray-400 mt-0.5">Starting from</span>
                                <span class="text-gray-900 font-bold text-lg sm:text-xl">
                                    Rp {{ number_format($destinasi->harga, 0, ',', '.') }}
                                </span>
                            </div>

                            <button class="px-4 sm:px-5 py-2 bg-[#496d9e] hover:bg-[#0f2942]
                                          text-white text-xs sm:text-sm font-semibold rounded-full
                                          transition-all duration-300">
                                Details
                            </button>
                        </div>
                    </div>
                </a>
                @endforeach

            </div>
        </div>
    </div>

    <!-- INDICATOR -->
    <div class="flex items-center justify-center gap-3 mt-6">
        <div id="destinasiDots" class="flex items-center gap-2"></div>
        <span id="slideCounter" class="text-xs text-gray-400 font-medium tabular-nums"></span>
    </div>

</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {

    let currentSlide = 0;
    let slidesToShow = 3;
    const MAX_DOTS   = 5;

    const track         = document.getElementById('destinasiTrack');
    const wrapper       = document.getElementById('destinasiWrapper');
    const prevBtn       = document.getElementById('scrollLeft');
    const nextBtn       = document.getElementById('scrollRight');
    const dotsContainer = document.getElementById('destinasiDots');
    const counter       = document.getElementById('slideCounter');
    const allCards      = Array.from(document.querySelectorAll('.destinasi-card'));

    const filterScroll = document.getElementById('filterScroll');
    const fadeLeft     = document.getElementById('filterFadeLeft');
    const fadeRight    = document.getElementById('filterFadeRight');

    function updateFilterFade() {
        const { scrollLeft, scrollWidth, clientWidth } = filterScroll;
        fadeLeft.classList.toggle('hidden',  scrollLeft <= 2);
        fadeRight.classList.toggle('hidden', scrollLeft + clientWidth >= scrollWidth - 2);
    }

    filterScroll.addEventListener('scroll', updateFilterFade);
    requestAnimationFrame(updateFilterFade);

    function getVisibleCards() {
        return allCards.filter(c => c.style.display !== 'none');
    }

    function updateSlidesToShow() {
        if (window.innerWidth < 640) {
            slidesToShow = 1;
            wrapper.style.overflowX = 'auto';
        } else if (window.innerWidth < 1024) {
            slidesToShow = 2;
            wrapper.style.overflowX = 'auto';
        } else {
            slidesToShow = 3;
            wrapper.style.overflowX = 'hidden';
        }
    }

    function updateSlider() {
        const visible = getVisibleCards();
        if (!visible.length) return;

        const gap       = 24;
        const cardWidth = visible[0].offsetWidth + gap;
        track.style.transform = `translateX(-${currentSlide * cardWidth}px)`;

        const maxSlide = Math.max(0, visible.length - slidesToShow);

        counter.textContent = maxSlide > 0
            ? `${currentSlide + 1} / ${maxSlide + 1}`
            : '';

        const dots  = Array.from(dotsContainer.children);
        const total = dots.length;

        let start = Math.max(0, currentSlide - Math.floor(MAX_DOTS / 2));
        let end   = start + MAX_DOTS - 1;
        if (end >= total) {
            end   = total - 1;
            start = Math.max(0, end - MAX_DOTS + 1);
        }

        dots.forEach((dot, i) => {
            dot.classList.toggle('dot-active', i === currentSlide);
            dot.classList.toggle('dot-hidden', i < start || i > end);
        });
    }

    function createDots() {
        dotsContainer.innerHTML = '';
        const visible  = getVisibleCards();
        const maxSlide = Math.max(0, visible.length - slidesToShow);

        for (let i = 0; i <= maxSlide; i++) {
            const dot = document.createElement('div');
            dot.className = 'dot';
            dot.addEventListener('click', () => {
                currentSlide = i;
                updateSlider();
            });
            dotsContainer.appendChild(dot);
        }
    }

    prevBtn?.addEventListener('click', () => {
        currentSlide = Math.max(0, currentSlide - 1);
        updateSlider();
    });

    nextBtn?.addEventListener('click', () => {
        const maxSlide = Math.max(0, getVisibleCards().length - slidesToShow);
        currentSlide   = Math.min(maxSlide, currentSlide + 1);
        updateSlider();
    });

    document.querySelectorAll('.filter-item').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-item')
                .forEach(b => b.classList.remove('filter-active'));
            btn.classList.add('filter-active');
            btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });

            const kategori = btn.dataset.kategori;
            currentSlide   = 0;

            allCards.forEach(card => {
                const show = kategori === 'all' || card.dataset.kategori === kategori;
                card.style.display = show ? '' : 'none';
            });

            track.style.transform = 'translateX(0)';
            updateSlidesToShow();
            createDots();
            updateSlider();
        });
    });

    window.addEventListener('resize', () => {
        currentSlide = 0;
        updateSlidesToShow();
        createDots();
        updateSlider();
        updateFilterFade();
    });

    updateSlidesToShow();
    createDots();
    updateSlider();
});
</script>