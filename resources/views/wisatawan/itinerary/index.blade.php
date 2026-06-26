@extends('layouts.app')

@section('title', 'AI Itinerary Planner — WayWay')

@push('styles')
<style>
    /* ===== GENERAL ===== */
    [x-cloak] { display: none !important; }

    /* Modal dark overlay */
    .modal-backdrop {
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(6px);
    }

    /* ===== CATEGORY PILLS ===== */
    .kat-pill { transition: all 0.15s ease; }
    .kat-pill.active { transform: scale(1.05); }

    /* ===== COMPANION CARDS ===== */
    .companion-card { transition: all 0.15s ease; cursor: pointer; }
    .companion-card:hover { transform: translateY(-2px); }
    .companion-card.selected {
        border-color: #0ea5e9 !important;
        background: #f0f9ff;
    }

    /* ===== LOCATION SEARCH DROPDOWN ===== */
    .search-dropdown {
        max-height: 220px;
        overflow-y: auto;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }
    .search-result-item { transition: background 0.1s ease; }
    .search-result-item:hover { background: #f0f9ff; }
</style>
@endpush

@section('content')
<div x-data="itineraryApp()" x-cloak>

{{-- ===================================================
     HERO SECTION
     Video background + CTA buttons
=================================================== --}}
<section class="relative w-full min-h-screen flex items-center justify-center overflow-hidden">

    {{-- VIDEO BACKGROUND --}}
    <video autoplay muted loop playsinline
        class="absolute inset-0 w-full h-full object-cover">
        <source src="{{ asset('videos/batam-hero.mp4') }}" type="video/mp4">
    </video>

    {{-- OVERLAY --}}
    <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-black/10 to-transparent"></div>

    {{-- FLOATING ORBS --}}
    <div class="absolute top-0 left-1/4 w-96 h-96 rounded-full blur-3xl -translate-y-1/2 opacity-20"
         style="background: #9FCCDA;"></div>
    <div class="absolute bottom-0 right-1/3 w-80 h-80 rounded-full blur-3xl translate-y-1/2 opacity-15"
         style="background: #5B9AC7;"></div>

    {{-- CONTENT --}}
    <div class="relative z-20 text-center px-4 sm:px-6 max-w-2xl text-white">

        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 px-4 py-1.5 rounded-full text-sm font-medium mb-6 text-blue-100">
            <span class="w-2 h-2 rounded-full animate-pulse" style="background: #9FCCDA;"></span>
            Smart Planning &middot; Optimized Routes
        </div>

        <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold leading-tight">
            Build Your<br>
            <span style="color: #F5DBB4;">Itinerary</span>
        </h1>

        <p class="mt-4 sm:mt-6 text-base sm:text-lg text-white/85 max-w-xl mx-auto leading-relaxed">
            Choose your interests, set a budget, and get a personalized Batam travel plan — complete with a map and hourly schedule.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-8">

            {{-- Plan My Trip → opens modal --}}
            <button @click="openModal()"
                class="inline-flex items-center gap-3 font-bold px-8 py-4 rounded-2xl text-base shadow-lg transition hover:bg-[#f9d497]"
                style="background:#F4DBB4; color:#1e293b;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Plan My Trip
            </button>

            {{-- My Trips → history page --}}
            <a href="{{ route('itinerary.history') }}"
               class="inline-flex items-center gap-3 font-bold px-8 py-4 rounded-2xl text-base shadow-lg
           bg-[#5B9AC7] text-white hover:bg-[#496d9e] transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                My Trips
            </a>
        </div>
    </div>

    <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-white to-transparent z-10"></div>
</section>

{{-- ===================================================
 {{-- LOADING OVERLAY — WayWay style --}}
<div x-show="isLoading"
     class="fixed inset-0 z-[9999] flex flex-col items-center justify-center"
     style="background: rgba(27,58,92,0.75); backdrop-filter: blur(8px);">

    {{-- Card putih --}}
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-sm mx-4 flex flex-col items-center gap-5 relative overflow-hidden">

        {{-- Kompas + Waybot --}}
        <div class="relative w-36 h-36 flex items-center justify-center">
            {{-- Ring luar --}}
            <div class="absolute w-36 h-36 rounded-full"
                 style="border:2.5px solid transparent; border-top-color:#4BA8C8; border-right-color:#E8C98A; animation:ww-spin 2s linear infinite;"></div>
            {{-- Ring dalam dashed --}}
            <div class="absolute w-28 h-28 rounded-full"
                 style="border:1.5px dashed rgba(91,200,212,0.4); animation:ww-spin-r 3.5s linear infinite;"></div>
            {{-- Titik kompas --}}
            <div class="absolute top-1 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full" style="background:#E8C98A;"></div>
            <div class="absolute bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full" style="background:#5BC8D4;"></div>
            <div class="absolute left-1 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full" style="background:#2B7FA8;"></div>
            <div class="absolute right-1 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full" style="background:#2B7FA8;"></div>

            {{-- Waybot --}}
            <div style="animation:ww-pulse 2.5s ease-in-out infinite; position:relative; z-index:2;">
                <div style="position:absolute; top:-14px; left:50%; transform:translateX(-50%); width:3px; height:10px; background:#1B3A5C; border-radius:2px;">
                    <div style="position:absolute; top:-6px; left:50%; transform:translateX(-50%); width:8px; height:8px; border-radius:50%; background:#4BA8C8; border:1.5px solid #2B7FA8;"></div>
                </div>
                <div style="width:68px; height:58px; background:white; border:2.5px solid #1B3A5C; border-radius:16px; display:flex; align-items:center; justify-content:center; position:relative; box-shadow:0 2px 12px rgba(43,127,168,0.15);">
                    <div style="position:absolute; left:-8px; top:50%; transform:translateY(-50%); width:9px; height:16px; background:#DCE8EF; border:2px solid #1B3A5C; border-radius:3px;"></div>
                    <div style="position:absolute; right:-8px; top:50%; transform:translateY(-50%); width:9px; height:16px; background:#DCE8EF; border:2px solid #1B3A5C; border-radius:3px;"></div>
                    <div style="width:48px; height:26px; background:#1B3A5C; border-radius:8px; display:flex; align-items:center; justify-content:center; gap:9px;">
                        <div style="width:13px; height:11px; border:2.5px solid #5BC8D4; border-bottom:none; border-radius:7px 7px 0 0; animation:ww-blink 4s ease-in-out infinite; transform-origin:center bottom;"></div>
                        <div style="width:13px; height:11px; border:2.5px solid #5BC8D4; border-bottom:none; border-left:none; border-radius:0 7px 0 0; animation:ww-blink 4s ease-in-out infinite 0.15s; transform-origin:center bottom;"></div>
                    </div>
                    <div style="position:absolute; bottom:-9px; left:50%; transform:translateX(-50%); width:0; height:0; border-left:7px solid transparent; border-right:7px solid transparent; border-top:9px solid #1B3A5C;"></div>
                    <div style="position:absolute; bottom:-6px; left:50%; transform:translateX(-50%); width:0; height:0; border-left:5px solid transparent; border-right:5px solid transparent; border-top:7px solid white;"></div>
                </div>
                <span style="position:absolute; top:6px; right:-18px; color:#E8C98A; font-size:10px; animation:ww-float 2s ease-in-out infinite;">✦</span>
                <span style="position:absolute; top:24px; left:-20px; color:#4BA8C8; font-size:7px; animation:ww-float 2.5s ease-in-out infinite 0.4s;">✦</span>
            </div>
        </div>

        {{-- Brand --}}
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full" style="background:#5BC8D4;"></div>
            <span class="font-semibold text-base" style="color:#1B3A5C;">WayWay</span>
        </div>

        {{-- Pipeline steps --}}
        <div class="flex items-center gap-1 w-full">
            <template x-for="(step, i) in pipeline" :key="i">
                <div class="flex items-center gap-1 flex-1">
                    <div class="flex flex-col items-center gap-1 flex-shrink-0">
                        <div :class="pipelineStep > i
                            ? 'w-8 h-8 rounded-full flex items-center justify-center text-xs text-white'
                            : pipelineStep === i
                                ? 'w-8 h-8 rounded-full flex items-center justify-center text-xs ring-2 animate-pulse'
                                : 'w-8 h-8 rounded-full flex items-center justify-center text-xs'"
                             :style="pipelineStep > i
                                ? 'background:#2B7FA8;'
                                : pipelineStep === i
                                    ? 'background:#EAF6FA; color:#2B7FA8; ring-color:#4BA8C8;'
                                    : 'background:#f1f5f9; color:#94a3b8;'">
                            <span x-text="pipelineStep > i ? '✓' : step.icon"></span>
                        </div>
                        <span class="text-xs whitespace-nowrap" style="color:#64748b;" x-text="step.label"></span>
                    </div>
                    <div x-show="i < pipeline.length - 1"
                         :style="pipelineStep > i ? 'flex:1; height:2px; background:#4BA8C8; margin-bottom:16px;' : 'flex:1; height:2px; background:#e2e8f0; margin-bottom:16px;'">
                    </div>
                </div>
            </template>
        </div>

        {{-- Pesan loading --}}
        <p class="text-sm font-medium animate-pulse" style="color:#2B7FA8;" x-text="loadingLabel"></p>

        {{-- Ombak bawah --}}
        <div class="absolute bottom-0 left-0 right-0 h-12 overflow-hidden pointer-events-none">
            <svg style="width:200%; animation:ww-wave 4s linear infinite;" viewBox="0 0 800 50" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 30 Q100 10 200 30 Q300 50 400 30 Q500 10 600 30 Q700 50 800 30 Q900 10 1000 30 Q1100 50 1200 30 Q1300 10 1400 30 Q1500 50 1600 30 L1600 50 L0 50 Z" fill="rgba(91,200,212,0.12)"/>
            </svg>
        </div>
    </div>
</div>
{{-- ===================================================
     MODAL STEPPER
     5 steps: Interests → Location → Companion → Date & Budget → Confirm
=================================================== --}}
<div x-show="showModal"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop"
     @click.self="closeModal()">

    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col"
         @click.stop>

        {{-- Modal header --}}
        <div class="px-6 pt-6 pb-4 border-b border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-slate-800 text-lg" x-text="steps[currentStep].title"></h2>
                <button @click="closeModal()"
                    class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Step dots --}}
            <div class="flex items-center gap-1">
                <template x-for="(step, i) in steps" :key="i">
                    <div class="flex items-center gap-1 flex-1">
                        <div :class="i < currentStep
                                ? 'w-7 h-7 rounded-full text-xs font-bold flex items-center justify-center bg-blue-500 text-white'
                                : i === currentStep
                                    ? 'w-7 h-7 rounded-full text-xs font-bold flex items-center justify-center bg-blue-100 text-blue-600 ring-2 ring-blue-400'
                                    : 'w-7 h-7 rounded-full text-xs font-bold flex items-center justify-center bg-slate-100 text-slate-400'"
                             x-text="i < currentStep ? '&#10003;' : (i + 1)">
                        </div>
                        <div x-show="i < steps.length - 1"
                            :class="i < currentStep ? 'flex-1 h-0.5 bg-blue-500' : 'flex-1 h-0.5 bg-slate-200'">
                        </div>
                    </div>
                </template>
            </div>
            <p class="text-xs text-slate-400 mt-2" x-text="steps[currentStep].subtitle"></p>
        </div>

        {{-- Modal body --}}
        <div class="flex-1 overflow-y-auto px-6 py-5">

            {{-- STEP 1: Interests --}}
            <div x-show="currentStep === 0">
                <div class="flex flex-wrap gap-2">
                    @foreach($kategoris as $kat)
                    <button type="button" @click="toggleKategori({{ $kat->id }})"
                        :class="form.kategori_ids.includes({{ $kat->id }})
                            ? 'kat-pill active px-4 py-2 rounded-xl text-sm font-semibold border-2 bg-blue-500 border-blue-500 text-white shadow-md'
                            : 'kat-pill px-4 py-2 rounded-xl text-sm font-semibold border-2 bg-white border-slate-200 text-slate-600 hover:border-blue-300 hover:bg-blue-50'">
                        {{ $kat->nama_kategori }}
                    </button>
                    @endforeach
                </div>
                <p x-show="errors.kategori" class="text-red-500 text-xs mt-3" x-text="errors.kategori"></p>
            </div>

            {{-- STEP 2: Location --}}
            <div x-show="currentStep === 1" class="space-y-3">
                <p class="text-sm text-slate-500">Search for your starting point in Batam:</p>
                <div class="relative">
                    <div class="relative flex items-center">

    <svg class="absolute left-5 w-5 h-5 text-slate-400"
        fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
    </svg>

    <input type="text"
        x-model="locationQuery"
        @input.debounce.500ms="searchLocation()"
        @keydown.escape="locationResults = []"
        placeholder="e.g. Nagoya Hill, Hotel XYZ..."
        class="w-full h-14 pl-14 pr-16 rounded-xl border border-slate-200 
        text-sm leading-none focus:outline-none focus:ring-2 focus:ring-blue-400">

    <button type="button"
        @click="useCurrentLocation()"
        class="absolute right-4 w-10 h-10 flex items-center justify-center
        bg-blue-50 hover:bg-blue-100 rounded-lg">

        <svg class="w-5 h-5 text-blue-600"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>

    </button>

</div>                    <div x-show="locationResults.length > 0"
                         class="search-dropdown absolute w-full bg-white border border-slate-200 rounded-xl mt-1 z-10">
                        <template x-for="(loc, i) in locationResults" :key="i">
                            <button type="button" @click="selectLocation(loc)"
                                class="search-result-item w-full text-left px-4 py-3 text-sm border-b border-slate-100 last:border-0">
                                <p class="font-medium text-slate-800 truncate" x-text="loc.display_name.split(',')[0]"></p>
                                <p class="text-xs text-slate-400 truncate" x-text="loc.display_name"></p>
                            </button>
                        </template>
                    </div>
                    <div x-show="isSearching" class="flex items-center gap-2 mt-2 text-xs text-slate-400">
                        <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Searching...
                    </div>
                </div>

                <div x-show="form.origin_label" class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-green-700 font-medium truncate" x-text="form.origin_label"></p>
                    <button type="button" @click="clearLocation()" class="ml-auto text-green-500 hover:text-green-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-slate-400">Powered by OpenStreetMap. Used to calculate the nearest route from your starting point.</p>
            </div>

            {{-- STEP 3: Companion --}}
            <div x-show="currentStep === 2">
                <div class="grid grid-cols-2 gap-3">
                    @php
                        $companions = [
                            'solo'     => ['label' => 'Solo',    'desc' => 'Traveling alone, total freedom!'],
                            'pasangan' => ['label' => 'Couple',  'desc' => 'Romantic trip for two'],
                            'keluarga' => ['label' => 'Family',  'desc' => 'Fun for the whole family'],
                            'grup'     => ['label' => 'Group',   'desc' => 'The more the merrier!'],
                        ];
                    @endphp
                    @foreach($companions as $val => $opt)
                    <button type="button" @click="form.companion = '{{ $val }}'"
                        :class="form.companion === '{{ $val }}'
                            ? 'companion-card selected p-4 rounded-2xl border-2 text-left'
                            : 'companion-card p-4 rounded-2xl border-2 border-slate-200 text-left hover:border-blue-300'">
                        <p class="font-bold text-slate-800 text-sm">{{ $opt['label'] }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $opt['desc'] }}</p>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- STEP 4: Date & Budget --}}
            <div x-show="currentStep === 3" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2">Visit Date</label>
                    <input type="date" x-model="form.tanggal" :min="today"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    <p x-show="errors.tanggal" class="text-red-500 text-xs mt-1" x-text="errors.tanggal"></p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2">Budget per Person (entrance fees)</label>
                    <div class="relative mb-3">
                        <span class="absolute left-5 top-[14px] text-slate-400 text-sm">Rp</span>
<input type="text"
    x-model="formattedBudget"
    @input="form.budget = parseInt($event.target.value.replace(/\./g, '')) || 0"
    class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    <div class="flex gap-2 flex-wrap">
                        @foreach($budgetOptions as $val => $label)
                        <button type="button" @click="form.budget = {{ $val }}"
                            :class="form.budget == {{ $val }}
                                ? 'px-3 py-1.5 text-xs rounded-lg border-2 bg-blue-500 text-white border-blue-500 font-semibold'
                                : 'px-3 py-1.5 text-xs rounded-lg border-2 border-slate-200 text-slate-500 hover:border-blue-300'">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2">
                        Max Destinations (<span x-text="form.max_destinations"></span> stops)
                    </label>
                    <input type="range" x-model="form.max_destinations" min="2" max="10" class="w-full accent-blue-500">
                    <div class="flex justify-between text-xs text-slate-400 mt-1">
                        <span>2 stops</span>
                        <span>10 stops</span>
                    </div>
                </div>
            </div>

            {{-- STEP 5: Confirm --}}
            <div x-show="currentStep === 4" class="space-y-4">
                <div class="bg-slate-50 rounded-2xl p-4 divide-y divide-slate-200">
                    <div class="flex items-center justify-between py-3">
                        <span class="text-sm text-slate-500">Interests</span>
                        <span class="text-sm font-semibold text-slate-700" x-text="form.kategori_ids.length + ' selected'"></span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-sm text-slate-500">Starting from</span>
                        <span class="text-sm font-semibold text-slate-700 text-right max-w-44 truncate" x-text="form.origin_label || 'Batam Center'"></span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-sm text-slate-500">Traveling with</span>
                        <span class="text-sm font-semibold text-slate-700" x-text="companionLabel"></span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-sm text-slate-500">Date</span>
                        <span class="text-sm font-semibold text-slate-700" x-text="form.tanggal"></span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-sm text-slate-500">Max budget</span>
                        <span class="text-sm font-semibold text-slate-700">Rp <span x-text="Number(form.budget).toLocaleString('id-ID')"></span></span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-sm text-slate-500">Max stops</span>
                        <span class="text-sm font-semibold text-slate-700" x-text="form.max_destinations + ' destinations'"></span>
                    </div>
                </div>
                <div x-show="errors.general" class="bg-red-50 border border-red-200 rounded-xl p-3 text-red-700 text-sm" x-text="errors.general"></div>
            </div>
        </div>

        {{-- Modal footer --}}
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between gap-3">
            <button x-show="currentStep > 0" @click="prevStep()"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </button>
            <div x-show="currentStep === 0" class="text-xs text-slate-400">Select at least 1 category</div>

            <button x-show="currentStep < steps.length - 1" @click="nextStep()"
                class="ml-auto flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold transition">
                Next
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <button x-show="currentStep === steps.length - 1" @click="generateItinerary()"
                class="ml-auto flex items-center gap-3 px-6 py-2.5 rounded-xl text-white text-sm font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300"
                style="background: linear-gradient(to right, #2563eb, #0891b2);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Generate Itinerary
            </button>
        </div>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script>
function itineraryApp() {
    return {
        // ==============================
        // MODAL STATE
        // ==============================
        showModal:   false,
        currentStep: 0,

        steps: [
            { title: 'Choose Your Interests',       subtitle: 'What do you want to explore today? Pick one or more.' },
            { title: 'Departure Location',           subtitle: 'Where will your journey start?' },
            { title: 'Who Are You Traveling With?', subtitle: 'We\'ll tailor recommendations based on your group.' },
            { title: 'Date & Budget',               subtitle: 'When are you going and what\'s your budget?' },
            { title: 'Review & Generate',           subtitle: 'Double-check your preferences before we start.' },
        ],

        // ==============================
        // PIPELINE (loading indicator)
        // ==============================
        pipeline: [
            { icon: '🔍', label: 'Filter'    },
            { icon: '📊', label: 'Bayesian'  },
            { icon: '📏', label: 'Haversine' },
            { icon: '🗺️', label: 'Route'     },
            { icon: '✅', label: 'OSRM'      },
        ],
        pipelineStep: 0,
        loadingLabel: 'Starting...',

        // ==============================
        // FORM DATA
        // ==============================
        form: {
            kategori_ids:     [],
            budget:           100000,
            companion:        'keluarga',
            tanggal:          new Date().toISOString().split('T')[0],
            origin_lat:       1.1296758,
            origin_lon:       104.0452254,
            origin_label:     '',
            max_destinations: 6,
            available_hours:  8,
        },

        // ==============================
        // LOCATION SEARCH
        // ==============================
        locationQuery:   '',
        locationResults: [],
        isSearching:     false,

        // ==============================
        // STATE
        // ==============================
        errors:    {},
        isLoading: false,

        get today() {
            return new Date().toISOString().split('T')[0];
        },
        get formattedBudget() {
            return this.form.budget
                ? Number(this.form.budget).toLocaleString('id-ID')
                : '';
        },

        set formattedBudget(val) {
            this.form.budget = parseInt(val.replace(/\./g, '')) || 0;
        },
                get companionLabel() {
            return { solo:'Solo', pasangan:'Couple', keluarga:'Family', grup:'Group' }[this.form.companion] || this.form.companion;
        },

        // ==============================
        // MODAL
        // ==============================
        openModal()  { this.showModal = true; this.currentStep = 0; this.errors = {}; },
        closeModal() { if (!this.isLoading) this.showModal = false; },

        // ==============================
        // CATEGORY
        // ==============================
        toggleKategori(id) {
            const idx = this.form.kategori_ids.indexOf(id);
            if (idx === -1) this.form.kategori_ids.push(id);
            else            this.form.kategori_ids.splice(idx, 1);
        },

        // ==============================
        // LOCATION SEARCH (Nominatim)
        // ==============================
        async searchLocation() {
            const q = this.locationQuery.trim();
            if (q.length < 3) { this.locationResults = []; return; }
            this.isSearching = true;
            try {
                const url = 'https://nominatim.openstreetmap.org/search'
                    + '?format=json&limit=5&addressdetails=1'
                    + '&viewbox=103.9,1.0,104.3,1.3&bounded=0'
                    + '&q=' + encodeURIComponent(q + ' Batam');
                const res  = await fetch(url, { headers: { 'Accept-Language': 'en' } });
                this.locationResults = await res.json();
            } catch (e) {
                this.locationResults = [];
            } finally {
                this.isSearching = false;
            }
        },

        selectLocation(loc) {
            this.form.origin_label = loc.display_name.split(',')[0];
            this.form.origin_lat   = parseFloat(loc.lat);
            this.form.origin_lon   = parseFloat(loc.lon);
            this.locationQuery     = '';
            this.locationResults   = [];
        },

        clearLocation() {
            this.form.origin_label = '';
            this.form.origin_lat   = 1.1296758;
            this.form.origin_lon   = 104.0452254;
        },

        useCurrentLocation() {
            if (!navigator.geolocation) { alert('Geolocation is not supported.'); return; }
            navigator.geolocation.getCurrentPosition(
                pos => {
                    this.form.origin_lat   = pos.coords.latitude;
                    this.form.origin_lon   = pos.coords.longitude;
                    this.form.origin_label = 'My Current Location';
                    this.locationResults   = [];
                },
                err => alert('Could not get location: ' + err.message)
            );
        },

        // ==============================
        // STEP NAV
        // ==============================
        validateStep() {
            this.errors = {};
            if (this.currentStep === 0 && !this.form.kategori_ids.length) {
                this.errors.kategori = 'Please select at least 1 category.';
                return false;
            }
            if (this.currentStep === 3 && !this.form.tanggal) {
                this.errors.tanggal = 'Please select a visit date.';
                return false;
            }
            return true;
        },

        nextStep() { if (!this.validateStep()) return; if (this.currentStep < this.steps.length - 1) this.currentStep++; },
        prevStep()  { if (this.currentStep > 0) this.currentStep--; },

        // ==============================
        // GENERATE → redirect to show page
        // ==============================
        async generateItinerary() {
            this.showModal = false;
            this.isLoading = true;
            this.errors    = {};

            const steps = [
                [0, 'Filtering 163 destinations...'],
                [1, 'Calculating scores...'],
                [2, 'Building distance matrix...'],
                [3, 'Optimizing route...'],
                [4, 'Validating via OSRM...'],
            ];

            for (const [step, label] of steps) {
                this.pipelineStep = step;
                this.loadingLabel = label;
                await new Promise(r => setTimeout(r, 500));
            }

            try {
                const response = await fetch('/itinerary/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify(this.form),
                });

                const json = await response.json();

                if (json.success) {
                    // Redirect ke halaman show dengan history_id
                    window.location.href = '/itinerary/show/' + json.data.history_id;
                } else {
                    this.errors.general = json.message || 'Something went wrong.';
                    this.showModal      = true;
                    this.currentStep    = 4;
                    this.isLoading      = false;
                }
            } catch (err) {
                this.errors.general = 'Connection failed. Please check your internet.';
                this.showModal      = true;
                this.currentStep    = 4;
                this.isLoading      = false;
            }
        },
    };
}
</script>

@endpush