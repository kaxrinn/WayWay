@extends('layouts.app')

@section('title', 'Semua Destinasi')

@section('content')

<section class="bg-white py-20">
    <div class="max-w-6xl mx-auto px-4">

        <!-- HEADER -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#496d9e] mt-4 mb-4">
                Semua Destinasi
            </h1>
@if(request('q'))
<p class="text-sm text-slate-500 mb-4">
    Menampilkan hasil yang berkaitan dengan
    <span class="font-semibold">“{{ request('q') }}”</span>
</p>
@endif

        <!-- GRID DESTINASI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($destinasi as $item)
                <a href="{{ route('destinasi.show', $item->id) }}"
                   class="bg-white rounded-[28px] shadow-md hover:shadow-xl
                          transition overflow-hidden group">

                    <div class="h-[200px]">
                        <img
                            src="{{ $item->foto ? Storage::url($item->foto[0]) : asset('images/placeholder.jpg') }}"
                            class="w-full h-full object-cover
                                   group-hover:scale-105 transition duration-500">
                    </div>

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
                            <span
                                class="px-4 py-2 bg-[#496d9e]
                                       text-white text-sm rounded-full">
                                Detail
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-3 text-center py-20 text-gray-400">
                    Destinasi tidak ditemukan
                </div>
            @endforelse
        </div>

<!-- PAGINATION -->
@if($destinasi->hasPages())
<div class="mt-12 flex flex-col items-center gap-3">
    
    <p class="text-sm text-gray-500">
        Menampilkan {{ $destinasi->firstItem() }}–{{ $destinasi->lastItem() }}
        dari <span class="font-semibold">{{ $destinasi->total() }}</span> destinasi
    </p>

    <div class="flex items-center gap-2 flex-wrap justify-center">

        {{-- Prev --}}
        @if($destinasi->onFirstPage())
            <span class="px-4 py-2 rounded-full bg-gray-100 text-gray-400 text-sm cursor-not-allowed">
                ← Prev
            </span>
        @else
            <a href="{{ $destinasi->previousPageUrl() }}"
               class="px-4 py-2 rounded-full bg-white border border-gray-200
                      text-sm text-gray-600 hover:bg-[#496d9e] hover:text-white transition">
                ← Prev
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach($destinasi->getUrlRange(1, $destinasi->lastPage()) as $page => $url)
            @if($page == $destinasi->currentPage())
                <span class="px-4 py-2 rounded-full bg-[#496d9e] text-white text-sm font-semibold">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}"
                   class="px-4 py-2 rounded-full bg-white border border-gray-200
                          text-sm text-gray-600 hover:bg-[#496d9e] hover:text-white transition">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        {{-- Next --}}
        @if($destinasi->hasMorePages())
            <a href="{{ $destinasi->nextPageUrl() }}"
               class="px-4 py-2 rounded-full bg-white border border-gray-200
                      text-sm text-gray-600 hover:bg-[#496d9e] hover:text-white transition">
                Next →
            </a>
        @else
            <span class="px-4 py-2 rounded-full bg-gray-100 text-gray-400 text-sm cursor-not-allowed">
                Next →
            </span>
        @endif

    </div>
</div>
@endif

    </div>
</section>
@endsection