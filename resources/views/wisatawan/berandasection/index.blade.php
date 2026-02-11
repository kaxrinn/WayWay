@extends('layouts.app')

@section('title', 'Semua Destinasi')

@section('content')

<section class="bg-white py-20">
    <div class="max-w-6xl mx-auto px-4">

        <!-- HEADER -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#496d9e] mb-4">
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
        <div class="mt-12">
            {{ $destinasi->withQueryString()->links() }}
        </div>

    </div>
</section>
@endsection