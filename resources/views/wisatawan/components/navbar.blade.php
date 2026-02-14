<head>
    <meta charset="UTF-8">
    <title>WayWay</title>

    <!-- TAILWIND -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- ALPINE JS (WAJIB) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ profileModal: false }">

<header class="fixed top-4 w-full z-50">
    <div class="max-w-[1200px] mx-auto px-4">
        <div class="bg-[#9ECCDB]/80 backdrop-blur
                    rounded-full
                    border border-sky-200
                    px-6 h-16
                    flex items-center justify-between">

            <!-- LOGO -->
            <div class="flex items-center gap-2">
                <img src="{{ asset('assets/logo/logoo.png') }}"
                     class="h-10" alt="WayWay">        
            <span class="font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#c6c4c9] via-[#415c7f] to-[#c6c4c9]">
                WayWay
            </span>
    </div>

            <!-- DESKTOP MENU -->
            <nav class="hidden lg:flex items-center gap-2 text-sm text-white">
                <a href="{{ route('wisatawan.beranda') }}"
                   class="px-4 py-2 rounded-full font-semibold
                           hover:bg-sky-100 hover:text-sky-600">
                    Beranda
                </a>

                <a href="/#destinasi"
                   class="px-4 py-2 rounded-full font-semibold
                           hover:bg-sky-100 hover:text-sky-600">
                    Destinasi
                </a>

                <a href="/#tentang"
                   class="px-4 py-2 rounded-full font-semibold
                           hover:bg-sky-100 hover:text-sky-600">
                    Tentang
                </a>

                <a href="/#kontak"
                   class="px-4 py-2 rounded-full font-semibold
                           hover:bg-sky-100 hover:text-sky-600">
                    Kontak
                </a>

                <a href="#"
                   class="px-4 py-2 rounded-full font-semibold
                          hover:bg-sky-100 hover:text-sky-600">
                    Itinerary
                </a>

                <a href="#"
                   class="px-4 py-2 rounded-full font-semibold
                          hover:bg-sky-100 hover:text-sky-600">
                    Chatbot
                </a>
            </nav>

            <!-- DESKTOP RIGHT -->
            <div class="hidden lg:flex items-center gap-3">
                <!-- SEARCH -->
                <form action="{{ route('destinasi.index') }}" method="GET" class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari destinasi..."
                        class="bg-white w-56 pl-10 pr-4 py-2 text-sm rounded-full
                               border border-sky-200 text-slate-600
                               focus:outline-none focus:ring-2 focus:ring-sky-300">

                    <button type="submit"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-sky-500">
                        <svg class="w-4 h-4"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35m1.85-5.65a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>

                @auth
                @php
                    $user = auth()->user();
                @endphp

                <div class="relative group">
                    <!-- ICON BUTTON -->
                    <button type="button"
                        class="w-10 h-10 rounded-full overflow-hidden
                               border-2 border-white shadow
                               hover:ring-2 hover:ring-sky-300 transition">

                        @if ($user && $user->avatar)
                            <img src="{{ $user->avatar }}"
                                 alt="Avatar"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-[#5b9ac7]
                                        flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 12c2.761 0 5-2.239 5-5
                                             s-2.239-5-5-5-5 2.239-5 5
                                             2.239 5 5 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 20c0-4.418 3.582-8 8-8
                                             s8 3.582 8 8"/>
                                </svg>
                            </div>
                        @endif
                    </button>

                    <!-- DROPDOWN -->
                    <div
                        class="absolute right-0 mt-3 w-44
                               bg-white rounded-xl shadow-lg
                               border border-sky-100
                               opacity-0 invisible translate-y-1
                               group-hover:opacity-100
                               group-hover:visible
                               group-hover:translate-y-0
                               transition-all duration-200 z-50">

                        <button
    @click="profileModal = true"
    type="button"
    class="flex items-center gap-2 px-4 py-2 text-sm
           text-slate-700 hover:bg-sky-50 rounded-t-xl w-full text-left">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
    </svg>
    Ubah Profil
</button>



                        {{-- ✅ LINK FAVORIT --}}
                        <a href="{{ route('wisatawan.favorit.index') }}"
                           class="flex items-center gap-2 px-4 py-2 text-sm
                                  text-slate-700 hover:bg-sky-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Favorit
                        </a>

                        <hr class="my-1">

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm
                                       text-red-500 hover:bg-red-50 rounded-b-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

                @else
                <a href="{{ route('wisatawan.login') }}"
                   class="px-5 py-2 text-sm rounded-full
                          bg-[#5b9ac7] text-white font-medium
                          hover:bg-[#496d9e] transition">
                    Login
                </a>
                @endauth
            </div>

            <!-- MOBILE SEARCH ICON -->
            <button id="mobileSearchBtn" class="lg:hidden p-2">
                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35m1.85-5.65a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- MOBILE SEARCH BAR (Hidden by default) -->
    <div id="mobileSearchBar" class="lg:hidden hidden mt-2 max-w-[1200px] mx-auto px-4">
        <div class="bg-white/90 backdrop-blur rounded-full px-4 py-3 shadow-lg border border-sky-200">
            <div class="relative">
                <form action="{{ route('destinasi.index') }}" method="GET" class="relative">
                    <input
                        type="text"
                        name="q"
                        placeholder="Cari destinasi..."
                        class="w-full pl-10 pr-4 py-2 text-sm rounded-full
                               border border-sky-200 text-slate-600
                               focus:outline-none focus:ring-2 focus:ring-sky-300">

                    <button type="submit"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="w-4 h-4"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35m1.85-5.65a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<!-- MODAL UBAH PROFIL -->
<div
    x-show="profileModal"
    x-cloak
    x-transition.opacity
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-[99999]"
>
    <div
        @click.away="profileModal = false"
        class="bg-white w-full max-w-lg rounded-2xl p-6 shadow-2xl relative"
    >

        <h2 class="text-xl font-bold text-slate-800 mb-6">
            Ubah Profil Wisatawan
        </h2>

        <form 
    method="POST" 
    action="{{ route('wisatawan.profile.update') }}"
    autocomplete="off">

            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium">Nama</label>
                    <input type="text" name="name"
                        value="{{ auth()->user()->name }}"
                        class="w-full rounded-xl border px-4 py-2">
                </div>

                <div>
                    <label class="text-sm font-medium">Email</label>
                    <input type="email" name="email"
                        value="{{ auth()->user()->email }}"
                        class="w-full rounded-xl border px-4 py-2">
                </div>

                <div>
                    <label class="text-sm font-medium">No Telepon</label>
                    <input type="text" name="no_telepon"
                        value="{{ auth()->user()->no_telepon }}"
                        class="w-full rounded-xl border px-4 py-2">
                </div>

                <div>
                    <label class="text-sm font-medium">Password Baru</label>
                    <input 
    type="password" 
    name="password"
    placeholder="Kosongkan jika tidak diubah"
    class="w-full rounded-xl border px-4 py-2"
    autocomplete="new-password"
>
                </div>

                <div>
                    <label class="text-sm font-medium">Konfirmasi Password</label>
                    <input 
    type="password" 
    name="password_confirmation"
    class="w-full rounded-xl border px-4 py-2"
    autocomplete="new-password"
>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button"
                    @click="profileModal = false"
                    class="px-4 py-2 rounded-full bg-gray-100">
                    Batal
                </button>

                <button type="submit"
                    class="px-5 py-2 rounded-full bg-[#5b9ac7] text-white">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
</div>

@if(session('success'))
<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 3000)"
    x-show="show"
    x-transition
    class="fixed top-24 right-6 bg-green-500 text-white px-5 py-3 rounded-xl shadow-lg z-[9999]">
    {{ session('success') }}
</div>
@endif


<!-- MOBILE BOTTOM NAVIGATION -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur border-t border-sky-200 shadow-lg">
    <div class="flex items-center justify-around h-16">
        
        <!-- Beranda -->
        <a href="{{ route('wisatawan.beranda') }}" 
           class="flex flex-col items-center justify-center flex-1 h-full text-xs font-medium text-gray-600 hover:text-[#5b9ac7] transition group">
            <svg class="w-6 h-6 mb-1 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Beranda</span>
        </a>

        <!-- Destinasi -->
        <a href="/#destinasi" 
           class="flex flex-col items-center justify-center flex-1 h-full text-xs font-medium text-gray-600 hover:text-[#5b9ac7] transition group">
            <svg class="w-6 h-6 mb-1 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Destinasi</span>
        </a>

        {{-- ✅ FAVORIT (Mobile) - Hanya tampil jika sudah login --}}
        @auth
        <a href="{{ route('wisatawan.favorit.index') }}" 
           class="flex flex-col items-center justify-center flex-1 h-full text-xs font-medium text-gray-600 hover:text-[#5b9ac7] transition group">
            <svg class="w-6 h-6 mb-1 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <span>Favorit</span>
        </a>
        @else
        <!-- Itinerary (jika belum login) -->
        <a href="#" 
           class="flex flex-col items-center justify-center flex-1 h-full text-xs font-medium text-gray-600 hover:text-[#5b9ac7] transition group">
            <svg class="w-6 h-6 mb-1 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            <span>Itinerary</span>
        </a>
        @endauth

        <!-- Chatbot -->
        <a href="#" 
           class="flex flex-col items-center justify-center flex-1 h-full text-xs font-medium text-gray-600 hover:text-[#5b9ac7] transition group">
            <svg class="w-6 h-6 mb-1 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            <span>Chatbot</span>
        </a>

        <!-- Profile/Account -->
        @auth
        <a href="{{ route('wisatawan.profile') }}" 
           class="flex flex-col items-center justify-center flex-1 h-full text-xs font-medium text-gray-600 hover:text-[#5b9ac7] transition group">
            @if (auth()->user()->avatar)
                <div class="w-7 h-7 mb-1 rounded-full overflow-hidden border-2 border-transparent group-hover:border-[#5b9ac7] transition">
                    <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="w-full h-full object-cover">
                </div>
            @else
                <svg class="w-6 h-6 mb-1 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            @endif
            <span>Profil</span>
        </a>
        @else
        <a href="{{ route('wisatawan.login') }}" 
           class="flex flex-col items-center justify-center flex-1 h-full text-xs font-medium text-gray-600 hover:text-[#5b9ac7] transition group">
            <svg class="w-6 h-6 mb-1 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            <span>Login</span>
        </a>
        @endauth
    </div>
    </div>
</nav>

<script>
// Mobile search toggle
const mobileSearchBtn = document.getElementById('mobileSearchBtn');
const mobileSearchBar = document.getElementById('mobileSearchBar');

if (mobileSearchBtn && mobileSearchBar) {
    mobileSearchBtn.addEventListener('click', () => {
        mobileSearchBar.classList.toggle('hidden');
    });
}

// Add active state to current page
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('nav a');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('text-[#5b9ac7]');
            link.classList.remove('text-gray-600');
        }
    });
});
</script>