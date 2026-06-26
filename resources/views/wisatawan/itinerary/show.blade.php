@extends('layouts.app')

@section('title', 'Your Itinerary — WayWay')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 420px; border-radius: 1rem; }
    .stop-card { transition: all 0.2s ease; }
    .stop-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
    .score-bar { transition: width 1s cubic-bezier(0.4,0,0.2,1); }
    .tip-card { transition: all 0.2s ease; }
    .tip-card:hover { transform: translateY(-1px); }
</style>
@endpush

@section('content')

{{-- ===================================================
     SHOW PAGE — Full itinerary result
     Loaded after generate redirects here
=================================================== --}}
<div class="max-w-5xl mx-auto px-6 py-30">

    {{-- Page header --}}
    <div class="flex items-start justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#496d9e]">Your Itinerary is Ready!</h1>
            <p class="text-slate-500 text-sm mt-1">
                {{ \Carbon\Carbon::parse($history->tanggal_kunjungan)->format('d F Y') }} &middot;
                @php
                    $companionLabels = ['solo'=>'Solo','pasangan'=>'Couple','keluarga'=>'Family','grup'=>'Group'];
                @endphp
                {{ $companionLabels[$history->companion] ?? $history->companion }} &middot;
                Budget Rp {{ number_format($history->budget, 0, ',', '.') }}
            </p>
        </div>

        <div class="flex gap-2 flex-shrink-0">
            {{-- Preview PDF --}}
            <a href="{{ route('itinerary.history.show', $history->id) }}" target="_blank"
                class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 border border-blue-200 rounded-xl text-sm font-medium hover:bg-blue-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Preview PDF
            </a>

            {{-- Download PDF --}}
            <a href="{{ route('itinerary.download', $history->id) }}"
                class="flex items-center gap-2 px-4 py-2 bg-[#fefaf6] text-[#496d9e] border border-[#d1c9b8] rounded-xl text-sm font-medium hover:bg-green-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download PDF
            </a>

            {{-- Re-plan --}}
            <a href="{{ route('itinerary.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-medium hover:bg-slate-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Re-plan
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-center">
            <p class="text-3xl font-bold text-[#496d9e]">{{ $history->stop_count }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium uppercase tracking-wide">Stops</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-center">
            <p class="text-3xl font-bold text-[#496d9e]">{{ $history->total_distance }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium uppercase tracking-wide">km Total</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-center">
            <p class="text-3xl font-bold text-[#496d9e]">{{ $history->formatted_duration }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium uppercase tracking-wide">Duration</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-center">
            @php $osrmOk = $history->result['osrm_validated'] ?? false; @endphp
            <div class="flex items-center justify-center gap-1.5 mb-1">
                <span class="w-2.5 h-2.5 rounded-full {{ $osrmOk ? 'bg-green-400' : 'bg-yellow-400' }}"></span>
                <p class="text-sm font-bold text-slate-700">{{ $osrmOk ? 'Validated' : 'Estimate' }}</p>
            </div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Route</p>
        </div>
    </div>

    {{-- Map --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-700">Route Map</h3>
            <span class="text-xs text-slate-400 bg-slate-50 px-3 py-1 rounded-full">OpenStreetMap</span>
        </div>
        <div id="map"></div>
    </div>

    {{-- Schedule --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm mb-6">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-700">Daily Schedule</h3>
            <p class="text-xs text-slate-400 mt-0.5">Click any destination to view full details</p>
        </div>
        <div class="p-5 space-y-3">
            @php $schedule = $history->result['schedule'] ?? []; @endphp

            @forelse($schedule as $item)
            @php $stop = $item['stop']; @endphp
            <a href="/destinasi/{{ $stop['id'] }}"
               class="stop-card flex gap-4 p-4 rounded-2xl border border-slate-100 hover:border-blue-200 bg-white block">

                {{-- Time --}}
                <div class="flex flex-col items-center flex-shrink-0 w-14">
                    <div class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1.5 rounded-lg text-center w-full">
                        {{ $item['arrival_time'] }}
                    </div>
                    {{-- Connector line: tampil di semua stop kecuali terakhir --}}
                    @if(!$loop->last)
                    <div class="w-0.5 flex-1 bg-gradient-to-b from-blue-200 to-transparent mt-2" style="min-height:24px"></div>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0 flex gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="w-6 h-6 bg-[#5B9AC7] text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                                {{ $stop['order'] }}
                            </span>
                            <h4 class="font-bold text-slate-800 text-sm truncate">{{ $stop['nama'] }}</h4>
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs text-slate-500 mb-2">
                            <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full font-medium">{{ $stop['kategori'] }}</span>
                            <span>Rp {{ number_format($stop['harga'], 0, ',', '.') }}</span>
                            <span>{{ $stop['visit_duration'] }} min visit</span>
                            @if($stop['order'] > 1)
                            <span>{{ $stop['road_duration_min'] ?? $stop['travel_minutes'] }} min travel</span>
                            @endif
                        </div>
                        {{-- Match score bar --}}
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400">Match</span>
                            <div class="flex-1 bg-slate-100 rounded-full h-1.5 max-w-28">
                                <div class="score-bar bg-gradient-to-r from-[#5B9AC7] to-[#9FCCDA] h-1.5 rounded-full"
                                    style="width: {{ round(($stop['bayesian_score'] ?? 0) * 100) }}%">
                                </div>
                            </div>
                            <span class="text-xs font-bold text-[#496d9e]">
                                {{ round(($stop['bayesian_score'] ?? 0) * 100) }}%
                            </span>
                        </div>
                    </div>

                    {{-- Thumbnail --}}
                    @if(!empty($stop['foto']) && is_array($stop['foto']) && count($stop['foto']) > 0)
                    <img src="/storage/{{ $stop['foto'][0] }}"
                        class="w-20 h-20 rounded-xl object-cover flex-shrink-0 shadow-sm"
                        alt="{{ $stop['nama'] }}"
                        onerror="this.style.display='none'">
                    @endif
                </div>
            </a>
            @empty
            <p class="text-slate-400 text-sm text-center py-8">No schedule data available.</p>
            @endforelse
        </div>
    </div>

    {{-- Travel Tips --}}
    @php
        $companion = $history->companion ?? 'solo';
        $tips = [
            'general' => [
                ['icon' => '🕐', 'title' => 'Start Early', 'desc' => 'Arrive at the first destination before 9 AM to beat the crowd and get the best experience.'],
                ['icon' => '💧', 'title' => 'Stay Hydrated', 'desc' => 'Batam\'s tropical heat can be intense. Bring a reusable bottle and refill it regularly.'],
                ['icon' => '📱', 'title' => 'Download Offline Maps', 'desc' => 'Save the route offline in Google Maps or Maps.me in case of poor signal at certain spots.'],
                ['icon' => '💵', 'title' => 'Bring Cash', 'desc' => 'Some smaller destinations or street food vendors may not accept cards. Prepare small bills.'],
                ['icon' => '🧴', 'title' => 'Sun Protection', 'desc' => 'Apply sunscreen before heading out. A hat and light long-sleeve shirt are highly recommended.'],
                ['icon' => '📷', 'title' => 'Golden Hour Shots', 'desc' => 'The best photos happen at sunrise or sunset. Plan outdoor stops around these times if possible.'],
            ],
            'solo' => [
                ['icon' => '🔒', 'title' => 'Keep Valuables Safe', 'desc' => 'Use a crossbody bag and keep your phone in your front pocket, especially in crowded areas.'],
                ['icon' => '🗣️', 'title' => 'Tell Someone Your Plan', 'desc' => 'Share your itinerary with a friend or family member back home before you head out.'],
            ],
            'pasangan' => [
                ['icon' => '🌅', 'title' => 'Golden Hour Spots', 'desc' => 'Check which stops have the best sunset views and plan your timing to catch the romantic light.'],
                ['icon' => '🍽️', 'title' => 'Book Dinner in Advance', 'desc' => 'Popular restaurants fill up fast on weekends. Reserve a table for a special end-of-day meal.'],
            ],
            'keluarga' => [
                ['icon' => '🧒', 'title' => 'Plan Kid-Friendly Breaks', 'desc' => 'Schedule rest stops every 90 minutes. Look for cafes or shaded areas between destinations.'],
                ['icon' => '🏥', 'title' => 'Locate Nearest Clinic', 'desc' => 'Save the address of the nearest clinic or pharmacy on your phone before heading out.'],
            ],
            'grup' => [
                ['icon' => '🚗', 'title' => 'Coordinate Transport', 'desc' => 'For groups of 5+, renting a minivan is usually cheaper and more comfortable than multiple taxis.'],
                ['icon' => '💬', 'title' => 'Create a Group Chat', 'desc' => 'Set up a WhatsApp group and share the itinerary link so everyone stays on the same page.'],
            ],
        ];

        $activeTips = array_merge($tips['general'], $tips[$companion] ?? []);
    @endphp

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm mb-6">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
            <div>
                <h2 class="text-2xl font-bold text-[#496d9e]">Travel Tips</h2>
                <p class="text-xs text-slate-400 mt-0.5">Personalized for your {{ $companionLabels[$companion] ?? $companion }} trip</p>
            </div>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($activeTips as $tip)
            <div class="tip-card flex gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-2xl flex-shrink-0 mt-0.5">{{ $tip['icon'] }}</span>
                <div>
                    <p class="text-sm font-bold text-slate-700 mb-0.5">{{ $tip['title'] }}</p>
                    <p class="text-xs text-slate-500 leading-relaxed">{{ $tip['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Technical meta footer --}}
    @php
        $meta         = $history->result['meta'] ?? [];
        $osrmValidated = $history->result['osrm_validated'] ?? false;
    @endphp
    <div class="bg-slate-50 rounded-2xl border border-slate-100 p-4 text-xs text-slate-400 space-y-1 mb-6">
        <p>
            <strong class="text-slate-600">{{ $meta['total_candidates'] ?? '-' }}</strong> destinations filtered &rarr;
            <strong class="text-slate-600">{{ $meta['total_ranked'] ?? '-' }}</strong> ranked &rarr;
            <strong class="text-slate-600">{{ $history->stop_count }}</strong> selected via Greedy TSP + Category Diversity
        </p>
        <p>
            Pipeline: Content Filter &rarr; Bayesian Scoring &rarr; Haversine Matrix &rarr; Greedy Route &rarr;
            {{ $osrmValidated ? 'OSRM Validated' : 'Haversine ×1.3 Estimate' }}
        </p>
    </div>

    {{-- My Trips link --}}
    <div class="text-center">
        <a href="{{ route('itinerary.history') }}" class="text-sm text-slate-400 hover:text-blue-600 transition">
            View all my trips &rarr;
        </a>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    @php
        $route        = $history->result['route']         ?? [];
        $origin       = $history->result['origin']        ?? ['lat' => 1.1296758, 'lon' => 104.0452254];
        $osrmGeometry = $history->result['osrm_geometry'] ?? null;
        $osrmValid    = $history->result['osrm_validated'] ?? false;
    @endphp

    const route        = @json($route);
    const origin       = @json($origin);
    const osrmGeometry = @json($osrmGeometry);
    const osrmValid    = @json($osrmValid);

    if (!route.length) return;

    const map = L.map('map').setView([route[0].latitude, route[0].longitude], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors', maxZoom: 18,
    }).addTo(map);

    // Origin marker
    const originIcon = L.divIcon({
        html: '<div style="width:36px;height:36px;background:white;border:3px solid #496d9e;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:13px;color:#496d9e;box-shadow:0 3px 10px rgba(0,0,0,0.2)">S</div>',
        className: '', iconSize: [36,36], iconAnchor: [18,18],
    });
    L.marker([origin.lat, origin.lon], { icon: originIcon })
        .addTo(map)
        .bindPopup('<b>Departure Point</b><br>{{ $history->origin_label ?: "Start" }}');

    const latlngs = [[origin.lat, origin.lon]];

    route.forEach(stop => {
        const icon = L.divIcon({
            html: '<div style="width:32px;height:32px;background:#5B9AC7;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:13px;border:2px solid white;box-shadow:0 3px 10px rgba(0,0,0,0.2)">' + stop.order + '</div>',
            className: '', iconSize: [32,32], iconAnchor: [16,16],
        });
        L.marker([stop.latitude, stop.longitude], { icon })
            .addTo(map)
            .bindPopup('<b>' + stop.nama + '</b><br><small>' + stop.kategori + '</small><br>Rp ' + Number(stop.harga).toLocaleString('id-ID'));
        latlngs.push([stop.latitude, stop.longitude]);
    });

    // Gambar rute: pakai OSRM geometry jika tersedia, fallback ke garis lurus
    if (osrmValid && osrmGeometry) {
        L.geoJSON(osrmGeometry, {
            style: { color: '#5B9AC7', weight: 3, opacity: 0.8, dashArray: '8,6' }
        }).addTo(map);
    } else {
        L.polyline(latlngs, { color: '#5B9AC7', weight: 3, opacity: 0.8, dashArray: '8,6' }).addTo(map);
    }

    map.fitBounds(L.latLngBounds(latlngs), { padding: [40,40] });
});
</script>
@endpush