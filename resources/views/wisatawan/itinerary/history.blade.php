@extends('layouts.app')

@section('title', 'My Trips — WayWay')

@section('content')

{{-- ===================================================
     MY TRIPS — History page
     Shows a paginated table of all past itineraries
=================================================== --}}
<section class="max-w-5xl mx-auto px-6 pt-32 pb-10 min-h-screen">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-[#496d9e] ">My Trips</h1>
            <p class="text-slate-500 text-sm mt-1">All your past AI-generated itineraries</p>
        </div>
        <a href="{{ route('itinerary.index') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#5B9AC7] text-white rounded-xl text-sm font-semibold hover:bg-blue-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Plan New Trip
        </a>
    </div>

    {{-- Success message --}}
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if($histories->count() > 0)

        {{-- History table --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50">
                            <th class="text-left px-5 py-4 font-semibold text-slate-600">Date</th>
                            <th class="text-left px-5 py-4 font-semibold text-slate-600">Starting From</th>
                            <th class="text-left px-5 py-4 font-semibold text-slate-600">With</th>
                            <th class="text-center px-5 py-4 font-semibold text-slate-600">Stops</th>
                            <th class="text-center px-5 py-4 font-semibold text-slate-600">Distance</th>
                            <th class="text-center px-5 py-4 font-semibold text-slate-600">Duration</th>
                            <th class="text-center px-5 py-4 font-semibold text-slate-600">Budget</th>
                            <th class="text-center px-5 py-4 font-semibold text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($histories as $h)
                        <tr class="hover:bg-slate-50 transition-colors">

                            {{-- Date --}}
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-800">
                                    {{ \Carbon\Carbon::parse($h->tanggal_kunjungan)->format('d M Y') }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    Generated {{ $h->created_at->diffForHumans() }}
                                </p>
                            </td>

                            {{-- Origin --}}
                            <td class="px-5 py-4">
                                <p class="text-slate-700 max-w-32 truncate">
                                    {{ $h->origin_label ?: 'Batam Center' }}
                                </p>
                            </td>

                            {{-- Companion --}}
                            <td class="px-5 py-4">
                                @php
                                    $companionLabels = [
                                        'solo'     => 'Solo',
                                        'pasangan' => 'Couple',
                                        'keluarga' => 'Family',
                                        'grup'     => 'Group',
                                    ];
                                @endphp
                                <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full text-xs font-medium">
                                    {{ $companionLabels[$h->companion] ?? $h->companion }}
                                </span>
                            </td>

                            {{-- Stop count --}}
                            <td class="px-5 py-4 text-center">
                                <span class="font-bold text-slate-800">{{ $h->stop_count }}</span>
                                <span class="text-slate-400 text-xs"> stops</span>
                            </td>

                            {{-- Distance --}}
                            <td class="px-5 py-4 text-center">
                                <span class="font-semibold text-slate-700">{{ $h->total_distance }}</span>
                                <span class="text-slate-400 text-xs"> km</span>
                            </td>

                            {{-- Duration --}}
                            <td class="px-5 py-4 text-center text-slate-700">
                                {{-- Gunakan accessor jika ada, fallback ke kalkulasi manual --}}
                                @php
                                    $mins = $h->total_minutes ?? 0;
                                    $dur  = $h->formatted_duration
                                         ?? ($mins >= 60
                                            ? floor($mins / 60) . 'h ' . ($mins % 60) . 'm'
                                            : $mins . 'm');
                                @endphp
                                {{ $dur }}
                            </td>

                            {{-- Budget --}}
                            <td class="px-5 py-4 text-center text-slate-700 text-xs">
                                Rp {{ number_format($h->budget, 0, ',', '.') }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">

                                    {{--
                                        TOMBOL MATA → show.blade.php (route: itinerary.show)
                                        BUKAN history.show (itu untuk PDF stream)
                                    --}}
                                    <a href="{{ route('itinerary.show', $h->id) }}"
                                        class="p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition"
                                        title="View Itinerary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    {{--
                                        TOMBOL UNDUH → PDF preview/stream di tab baru
                                        Pakai historyShow() yang return $pdf->stream(...)
                                    --}}
                                    <a href="{{ route('itinerary.history.show', $h->id) }}" target="_blank"
                                        class="p-1.5 bg-green-50 hover:bg-green-100 text-green-600 rounded-lg transition"
                                        title="Unduh / Preview PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('itinerary.history.delete', $h->id) }}"
                                        onsubmit="return confirm('Delete this itinerary?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($histories->hasPages())
        <div class="mt-6">
            {{ $histories->links() }}
        </div>
        @endif

    @else
        {{-- Empty state --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
            <div class="w-20 h-20 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-700 mb-2">No trips yet</h3>
            <p class="text-slate-400 text-sm mb-6 max-w-sm mx-auto">
                You haven't generated any itineraries yet. Start planning your first Batam adventure!
            </p>
            <a href="{{ route('itinerary.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500 text-white rounded-xl font-semibold text-sm hover:bg-blue-600 transition">
                Plan My First Trip
            </a>
        </div>
    @endif

</section>

@endsection