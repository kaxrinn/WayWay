<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false, userMenuOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - WayWay Pemilik</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#9eccdb',
                        accent: '#f4dbb4',
                        dark: '#4e4e4e',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-50 font-sans">
    
    <nav class="bg-gradient-to-r from-primary to-blue-400 text-white shadow-lg fixed w-full top-0 z-50">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="lg:hidden text-white hover:bg-white/20 p-2 rounded-lg transition">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <div class="flex items-center gap-3">
                    <div class="bg-white p-2 rounded-lg">
    <img src="{{  asset('assets/Logo/logo.png')}}" 
         alt="Logo" 
         class="w-8 h-8 object-contain">
</div>
                        
                        <div>
                            <h1 class="text-2xl font-bold">WayWay</h1>
                            <p class="text-xs text-white/80">Pemilik Wisata</p>
                        </div>
                    </div>
                </div>
                
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="flex items-center gap-3 hover:bg-white/20 px-4 py-2 rounded-lg transition">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=f4dbb4&color=4e4e4e" 
                             alt="Avatar" 
                             class="w-10 h-10 rounded-full border-2 border-white">
                        <div class="hidden md:block text-left">
                            <p class="font-semibold text-sm">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-white/80">
                                @php
                                    $userPaket = auth()->user()->currentPaket;
                                    echo $userPaket ? $userPaket->nama_paket : 'Basic';
                                @endphp
                            </p>
                        </div>
                        <i class="fas fa-chevron-down text-sm"></i>
                    </button>
                    
                    <div x-show="open" 
                         @click.away="open = false"
                         x-cloak
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-2 z-50">
                        <a href="{{ route('pemilik.profile') }}" 
                           class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                            <i class="fas fa-user mr-2"></i> Profil
                        </a>
                        <a href="{{ route('pemilik.paket.index') }}" 
                           class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                            <i class="fas fa-box mr-2"></i> Upgrade Paket
                        </a>
                        <hr class="my-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 transition">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed left-0 top-0 h-full w-64 bg-white shadow-2xl z-40 transition-transform duration-300 lg:translate-x-0 pt-16">
        
        <div class="p-6">
            @php
                $paket = auth()->user()->currentPaket;
                $paketName = $paket ? $paket->nama_paket : 'Basic';
                $colors = [
                    'Basic' => 'bg-blue-100 text-blue-800',
                    'Standard' => 'bg-purple-100 text-purple-800',
                    'Premium' => 'bg-yellow-100 text-yellow-800',
                ];
                $color = $colors[$paketName] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <div class="{{ $color }} px-4 py-2 rounded-lg text-center mb-6">
                <p class="text-xs font-semibold">Paket Aktif</p>
                <p class="text-lg font-bold">{{ $paketName }}</p>
            </div>
            
            <nav class="space-y-2">
                <a href="{{ route('pemilik.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary/20 transition {{ request()->routeIs('pemilik.dashboard') ? 'bg-primary/30 text-primary font-semibold' : 'text-gray-700' }}">
                    <i class="fas fa-home w-5"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('pemilik.destinasi.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary/20 transition {{ request()->routeIs('pemilik.destinasi.*') ? 'bg-primary/30 text-primary font-semibold' : 'text-gray-700' }}">
                    <i class="fas fa-map-marked-alt w-5"></i>
                    <span>Destinasi Saya</span>
                </a>
                
                <a href="{{ route('pemilik.paket.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary/20 transition {{ request()->routeIs('pemilik.paket.*') ? 'bg-primary/30 text-primary font-semibold' : 'text-gray-700' }}">
                    <i class="fas fa-box w-5"></i>
                    <span>Paket Promosi</span>
                </a>
                
                @php
                    $canEditFoto = $paket && $paket->can_edit_foto;
                @endphp
                @if(!$canEditFoto)
                <a href="{{ route('pemilik.edit-request.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary/20 transition {{ request()->routeIs('pemilik.edit-request.*') ? 'bg-primary/30 text-primary font-semibold' : 'text-gray-700' }}">
                    <i class="fas fa-edit w-5"></i>
                    <span>Edit Request</span>
                    @if(($pendingCount ?? 0) > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>
                @endif
                
                <!-- bantuan link -->
              <a href="https://wa.me/6289520428618?text={{ urlencode('Halo admin WayWay, saya butuh bantuan') }}"
   target="_blank"
   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary/20 transition text-gray-700">
    <i class="fas fa-question-circle w-5"></i>
    <span>Bantuan</span>
</a>
            </nav>
        </div>
    </aside>
    
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"></div>
    
    <main class="lg:ml-64 pt-16 min-h-screen">
        <div class="p-6">
            @yield('content')
        </div>
    </main>
    
    @stack('scripts')
</body>
</html>