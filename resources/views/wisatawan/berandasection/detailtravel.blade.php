@extends('layouts.app')

@section('title', $package->nama_paket . ' — WayWay')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-21 pt-30">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left column: main info --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Thumbnail --}}
            <div class="rounded-2xl overflow-hidden border border-gray-200">
                @if ($package->thumbnail)
                    <img src="{{ Storage::url($package->thumbnail) }}"
                         alt="{{ $package->nama_paket }}"
                         class="w-full h-auto block">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-blue-100 to-cyan-200 flex items-center justify-center">
                        <svg class="w-16 h-16 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Title & badges --}}
            <div>
                <div class="flex flex-wrap gap-2 mb-2">
                    <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full">
                        {{ $package->durasi_hari }} Days
                    </span>
                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                        {{ \Carbon\Carbon::parse($package->tanggal_keberangkatan)->format('F d, Y') }}
                    </span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $package->nama_paket }}</h1>
                <p class="mt-1 text-sm text-gray-500">By <span class="font-medium text-gray-700">{{ $package->travelAgent->name ?? 'Travel Agent' }}</span></p>
            </div>

            {{-- Description --}}
            <div>
                <h2 class="text-base font-semibold text-gray-900 mb-2">About This Package</h2>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $package->deskripsi }}</p>
            </div>

            {{-- Destination --}}
            @if ($package->destinasi)
            <div>
                <h2 class="text-base font-semibold text-gray-900 mb-3">Destination</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($package->destinasi as $dest)
                    <span class="bg-blue-50 text-blue-700 text-sm px-3 py-1 rounded-full border border-blue-100">📍 {{ $dest }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Itinerary --}}
            @if ($package->itinerary && count($package->itinerary) > 0)
            <div>
                <h2 class="text-base font-semibold text-gray-900 mb-3">Itinerary</h2>
                <div class="space-y-3">
                    @foreach ($package->itinerary as $i => $item)
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-7 h-7 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 bg-gray-50 rounded-lg px-4 py-2.5 text-sm text-gray-700">{{ $item }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Right column: booking sidebar --}}
        <div class="lg:col-span-1">
            <div class="sticky top-6 space-y-4">

                {{-- Price & participants --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <p class="text-xs text-gray-400 mb-1">Price per Person</p>
                    <p class="text-3xl font-bold text-blue-600 mb-1">
                        Rp {{ number_format($package->harga_per_orang, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500 mb-4">
                        Min. {{ $package->min_peserta }} – Max. {{ $package->max_peserta }} Participants
                    </p>

                    <div class="space-y-2 text-sm text-gray-600 mb-5">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ \Carbon\Carbon::parse($package->tanggal_keberangkatan)->format('F d, Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <span>{{ $package->meeting_point }}</span>
                        </div>
                    </div>

                    {{-- WhatsApp button --}}
                    @if ($package->whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $package->whatsapp) }}?text={{ urlencode('Hello, I am interested in the package ' . $package->nama_paket) }}"
                       target="_blank"
                       class="w-full flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-xl transition-colors text-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Contact via WhatsApp
                    </a>
                    @endif
                </div>

                {{-- Agent contact info --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 space-y-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Agent Information</p>
                    @if ($package->email)
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <a href="mailto:{{ $package->email }}" class="hover:text-blue-600 transition-colors">{{ $package->email }}</a>
                    </div>
                    @endif
                    @if ($package->instagram)
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                        <a href="https://instagram.com/{{ ltrim($package->instagram, '@') }}" target="_blank" class="hover:text-blue-600 transition-colors">{{ $package->instagram }}</a>
                    </div>
                    @endif
                    @if ($package->website)
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                        </svg>
                        <a href="{{ $package->website }}" target="_blank" class="hover:text-blue-600 transition-colors truncate">{{ $package->website }}</a>
                    </div>
                    @endif
                </div>

                {{-- Include / Exclude --}}
                <div class="space-y-3">
                    @if ($package->fasilitas_include)
                    <div class="bg-green-50 rounded-xl p-4">
                        <h3 class="text-sm font-semibold text-green-800 mb-3 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Included
                        </h3>
                        <ul class="space-y-1.5">
                            @foreach ($package->fasilitas_include as $item)
                            <li class="text-sm text-green-700 flex items-start gap-2">
                                <span class="mt-0.5 text-green-400">✓</span> {{ $item }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if ($package->fasilitas_exclude)
                    <div class="bg-red-50 rounded-xl p-4">
                        <h3 class="text-sm font-semibold text-red-800 mb-3 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Not Included
                        </h3>
                        <ul class="space-y-1.5">
                            @foreach ($package->fasilitas_exclude as $item)
                            <li class="text-sm text-red-700 flex items-start gap-2">
                                <span class="mt-0.5 text-red-400">✗</span> {{ $item }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

</div>

<div class="flex justify-center mt-7 mb-8">
    <a href="{{ route('wisatawan.beranda') }}"
       class="bg-[#5b9ac7] hover:bg-[#496d9e]
              text-white font-medium
              rounded-full px-6 py-2 text-sm transition">
        Back to Home
    </a>
</div>
@endsection