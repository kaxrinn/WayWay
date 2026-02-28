@extends('layouts.app')

@section('title', 'All Destinations')

@section('content')

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
    border: none;
    background: transparent;
    font-size: 0.875rem;
}
.filter-item:hover { background: #f9d497; }
.filter-active { background: rgb(244, 219, 180); color: #4E4E4E; }
.filter-fade-right { background: linear-gradient(to right, transparent, white 80%); }
.filter-fade-left  { background: linear-gradient(to left,  transparent, white 80%); }
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<section class="bg-white py-20">
    <div class="max-w-6xl mx-auto px-4">

        <!-- HEADER -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#496d9e] mt-4 mb-4">
                All Destinations
            </h1>

            @if(request('q'))
            <p class="text-sm text-slate-500 mb-4">
                Showing results for
                <span class="font-semibold">"{{ request('q') }}"</span>
            </p>
            @endif

            <!-- FILTER SCROLL -->
            <div class="relative">
                <div id="filterFadeLeft"
                     class="filter-fade-left pointer-events-none absolute left-0 top-0 h-full w-10 z-10 hidden">
                </div>

                <div id="filterScroll" class="flex gap-2 overflow-x-auto scrollbar-hide pb-1">
                    <a href="{{ route('destinasi.index', array_merge(request()->except('kategori', 'page'), [])) }}"
                       class="filter-item {{ !request('kategori') || request('kategori') === 'all' ? 'filter-active' : '' }}">
                        All
                    </a>
                    @foreach($kategori as $kat)
                    <a href="{{ route('destinasi.index', array_merge(request()->except('kategori', 'page'), ['kategori' => $kat->id])) }}"
                       class="filter-item {{ request('kategori') == $kat->id ? 'filter-active' : '' }}">
                        {{ $kat->nama_kategori }}
                    </a>
                    @endforeach
                </div>

                <div id="filterFadeRight"
                     class="filter-fade-right pointer-events-none absolute right-0 top-0 h-full w-10 z-10">
                </div>
            </div>
        </div>

        <!-- DESTINATION GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($destinasi as $item)
                <a href="{{ route('destinasi.show', $item->id) }}"
                   class="bg-white rounded-[28px] shadow-md hover:shadow-xl transition overflow-hidden group">

                    <div class="h-[200px]">
                        <img src="{{ $item->foto ? Storage::url($item->foto[0]) : asset('images/placeholder.jpg') }}"
                             alt="{{ $item->nama_destinasi }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    </div>

                    <div class="p-5">
                        <h3 class="font-semibold text-gray-800 text-lg">
                            {{ $item->nama_destinasi }}
                        </h3>

                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                            {{ Str::limit($item->deskripsi, 80) }}
                        </p>

                        <div class="flex items-center justify-between mt-4">
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-400">Starting from</span>
                                <span class="text-[#496d9e] font-bold">
                                    Rp {{ number_format($item->harga, 0, ',', '.') }}
                                </span>
                            </div>
                            <span class="px-4 py-2 bg-[#496d9e] text-white text-sm rounded-full">
                                Details
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-3 text-center py-20 text-gray-400">
                    No destinations found
                </div>
            @endforelse
        </div>

        <!-- PAGINATION -->
        @if($destinasi->hasPages())
        <div class="mt-12 flex flex-col items-center gap-3">

            <p class="text-sm text-gray-500">
                Showing {{ $destinasi->firstItem() }}–{{ $destinasi->lastItem() }}
                of <span class="font-semibold">{{ $destinasi->total() }}</span> destinations
            </p>

            <div class="flex items-center gap-2 flex-wrap justify-center">

                @if($destinasi->onFirstPage())
                    <span class="px-4 py-2 rounded-full bg-gray-100 text-gray-400 text-sm cursor-not-allowed">← Prev</span>
                @else
                    <a href="{{ $destinasi->previousPageUrl() }}"
                       class="px-4 py-2 rounded-full bg-white border border-gray-200 text-sm text-gray-600 hover:bg-[#496d9e] hover:text-white transition">
                        ← Prev
                    </a>
                @endif

                @foreach($destinasi->getUrlRange(1, $destinasi->lastPage()) as $page => $url)
                    @if($page == $destinasi->currentPage())
                        <span class="px-4 py-2 rounded-full bg-[#496d9e] text-white text-sm font-semibold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}"
                           class="px-4 py-2 rounded-full bg-white border border-gray-200 text-sm text-gray-600 hover:bg-[#496d9e] hover:text-white transition">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                @if($destinasi->hasMorePages())
                    <a href="{{ $destinasi->nextPageUrl() }}"
                       class="px-4 py-2 rounded-full bg-white border border-gray-200 text-sm text-gray-600 hover:bg-[#496d9e] hover:text-white transition">
                        Next →
                    </a>
                @else
                    <span class="px-4 py-2 rounded-full bg-gray-100 text-gray-400 text-sm cursor-not-allowed">Next →</span>
                @endif

            </div>
        </div>
        @endif

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const filterScroll = document.getElementById('filterScroll');
    const fadeLeft     = document.getElementById('filterFadeLeft');
    const fadeRight    = document.getElementById('filterFadeRight');

    function updateFade() {
        const { scrollLeft, scrollWidth, clientWidth } = filterScroll;
        fadeLeft.classList.toggle('hidden', scrollLeft <= 2);
        fadeRight.classList.toggle('hidden', scrollLeft + clientWidth >= scrollWidth - 2);
    }

    filterScroll.addEventListener('scroll', updateFade);
    requestAnimationFrame(updateFade);

    const activeBtn = filterScroll.querySelector('.filter-active');
    if (activeBtn) activeBtn.scrollIntoView({ block: 'nearest', inline: 'center' });
});
</script>

@endsection