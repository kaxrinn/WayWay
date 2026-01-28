<header class="fixed top-4 w-full z-50">
    <div class="max-w-[1200px] mx-auto px-4">

        <div class="bg-[#9ECCDB]/80 backdrop-blur
                    rounded-full
                    border border-sky-200
                    px-6 h-16
                    flex items-center justify-between">

            <!-- LOGO -->
            <a href="/" class="flex items-center gap-2">
                <img src="{{ asset('assets/logo/logoo.png') }}"
                     class="h-10" alt="WayWay">
                <span class="font-bold text-[#415c7f] bg-[#9ECCDB]">
                    Way<span class="text-white bg-[#9ECCDB]">Way</span>
                </span>
            </a>

            <!-- MENU -->
            <nav class="hidden lg:flex items-center gap-2 text-sm text-white">

    <!-- SECTION (1 halaman) -->
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

    <!-- HALAMAN BARU -->
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
    </nav>
    <!-- RIGHT -->
    <div class="hidden lg:flex items-center gap-3">
    <!-- SEARCH -->
    <div class="relative">
        <input
            type="text"
            placeholder="Cari destinasi..."
            class="bg-white w-56 pl-10 pr-4 py-2 text-sm rounded-full
                   border border-sky-200 text-slate-600
                   focus:outline-none focus:ring-2 focus:ring-sky-300">

        <!-- ICON SEARCH -->
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-4.35-4.35m1.85-5.65a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </div>
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

        <a href="{{ route('wisatawan.profile') }}"
           class="flex items-center gap-2 px-4 py-2 text-sm
                  text-slate-700 hover:bg-sky-50 rounded-t-xl">
            Profil
        </a>

        <a href="#"
           class="flex items-center gap-2 px-4 py-2 text-sm
                  text-slate-700 hover:bg-sky-50">
            Favorit
        </a>

        <hr class="my-1">

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full text-left px-4 py-2 text-sm
                       text-red-500 hover:bg-red-50 rounded-b-xl">
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

            <!-- MOBILE BTN -->
            <button id="mobileMenuBtn" class="lg:hidden">
                <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

        </div>
    </div>
</header>

    </div>
</header>
<script>
document.getElementById('mobileMenuBtn')
  .onclick = () =>
    document.getElementById('mobileMenu').classList.toggle('hidden');
</script>
