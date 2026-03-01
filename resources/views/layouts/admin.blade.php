<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Admin Dashboard') - WayWay</title>

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Google Fonts -->
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
                fontFamily: { sans: ['Poppins', 'sans-serif'] }
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>

<!-- Alpine.js for interactivity -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

@stack('styles')
</head>

<body class="bg-gray-100 font-sans" x-data="{ sidebarOpen: false, profileModal: false }">

<!-- Success Alert -->
@if(session('success'))
<div 
    x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(() => show = false, 3000)"
    x-transition.opacity
    class="fixed top-20 right-6 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
>
    <i class="fas fa-check-circle mr-2"></i>
    {{ session('success') }}
</div>
@endif

<!-- Navbar -->
<nav class="bg-gradient-to-r from-primary to-blue-400 shadow-lg fixed w-full z-30">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo & Hamburger -->
            <div class="flex items-center">
                <button @click="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar" class="text-white p-2 rounded-md lg:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center ml-4 lg:ml-0">
                    <img src="{{ asset('assets/Logo/logo.png')}}" alt="WayWay Logo" class="h-10 w-10">
                    <span class="ml-3 font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#c6c4c9] via-[#415c7f] to-[#c6c4c9]">
                        WayWay
                    </span>
                </div>
            </div>

            <!-- User Menu -->
            <div class="flex items-center" x-data="{ userMenuOpen: false }">
                <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-3 text-white hover:bg-white/20 rounded-full px-4 py-2 transition">
                    <img src="{{ auth()->user()->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=9eccdb&color=fff' }}" 
                         alt="Profile" 
                         class="h-8 w-8 rounded-full border-2 border-white">
                    <span class="font-medium">{{ auth()->user()->name }}</span>
                    <i class="fas fa-chevron-down text-sm"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="userMenuOpen" 
                     @click.away="userMenuOpen = false"
                     x-cloak
                     x-transition.opacity
                     x-transition.duration.200ms
                     class="absolute right-0 top-16 w-48 bg-white rounded-lg shadow-xl py-2 z-50">
                    <button
                     @click="profileModal = true; userMenuOpen = false"
                     class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-accent transition">
                     <i class="fas fa-user-edit mr-2"></i> Edit Profile
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-accent transition">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Error Alert -->
@if ($errors->any())
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show=false, 5000)" x-transition.opacity
    class="fixed top-20 right-6 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <ul class="list-disc ml-4">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Modal Edit Profile -->
<div x-show="profileModal" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
     role="dialog" aria-modal="true">

    <div @click.away="profileModal = false"
         class="bg-white rounded-xl w-full max-w-lg p-6 shadow-xl">

        <h2 class="text-xl font-bold mb-6 text-gray-800">Edit Admin Profile</h2>

        <form method="POST" action="{{ route('admin.profile.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name"
                           value="{{ auth()->user()->name }}"
                           class="w-full rounded-lg border-2 border-primary/50 bg-gray-50 px-4 py-2 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/30 transition">
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email"
                           value="{{ auth()->user()->email }}"
                           class="w-full rounded-lg border-2 border-primary/50 bg-gray-50 px-4 py-2 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/30 transition">
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" name="no_telepon"
                           value="{{ auth()->user()->no_telepon }}"
                           placeholder="08xxxxxxxxxx"
                           class="w-full rounded-lg border-2 border-primary/50 bg-gray-50 px-4 py-2 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/30 transition">
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">New Password</label>
                    <div class="relative">
                        <input type="password" name="password" autocomplete="new-password" placeholder="Leave blank if unchanged"
                               class="w-full rounded-lg border-2 border-accent/70 bg-gray-50 px-4 py-2 focus:bg-white focus:border-accent focus:ring-2 focus:ring-accent/30 transition"
                               id="newPassword">
                        <i class="fas fa-eye-slash absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer text-gray-600"
                           id="toggleNewPassword"></i>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                           class="w-full rounded-lg border-2 border-accent/70 bg-gray-50 px-4 py-2 focus:bg-white focus:border-accent focus:ring-2 focus:ring-accent/30 transition"
                           id="confirmNewPassword">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" @click="profileModal = false"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">Cancel</button>
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-primary text-white hover:bg-primary/80 transition">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Sidebar -->
<aside 
    class="fixed left-0 top-16 h-screen w-72 bg-white shadow-xl transform transition-transform duration-300 lg:translate-x-0 z-20"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <nav class="mt-5 px-2 space-y-1">

        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-100 transition
           {{ request()->routeIs('admin.dashboard') ? 'bg-blue-200 text-blue-800 font-semibold' : '' }}">
            <i class="fas fa-home w-5"></i>
            <span class="ml-3 truncate">Dashboard</span>
        </a>

        <!-- Manage Users -->
        <div x-data="{ openUsers: {{ request()->routeIs('admin.wisatawan.*') || request()->routeIs('admin.pemilik.*') ? 'true' : 'false' }} }">
            <button @click="openUsers = !openUsers" 
                    class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-100 transition">
                <div class="flex items-center">
                    <i class="fas fa-users w-5"></i>
                    <span class="ml-3 truncate">Manage Users</span>
                </div>
                <i class="fas fa-chevron-down transition-transform" :class="openUsers ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="openUsers" x-cloak class="ml-6 mt-2 space-y-1">
                <a href="{{ route('admin.wisatawan.index') }}" 
                   class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-100 transition
                   {{ request()->routeIs('admin.wisatawan.*') ? 'bg-blue-50 font-medium text-blue-700' : '' }}">
                    Tourists
                </a>
                <a href="{{ route('admin.pemilik.index') }}" 
                   class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-100 transition
                   {{ request()->routeIs('admin.pemilik.*') ? 'bg-blue-50 font-medium text-blue-700' : '' }}">
                    Owners
                </a>
            </div>
        </div>

        <!-- Manage Data -->
        <div x-data="{ openData: {{ request()->routeIs('admin.destinasi.*') || request()->routeIs('admin.kategori.*') ? 'true' : 'false' }} }">
            <button @click="openData = !openData" 
                    class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-100 transition">
                <div class="flex items-center">
                    <i class="fas fa-database w-5"></i>
                    <span class="ml-3 truncate">Manage Data</span>
                </div>
                <i class="fas fa-chevron-down transition-transform" :class="openData ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="openData" x-cloak class="ml-6 mt-2 space-y-1">
                <a href="{{ route('admin.destinasi.index') }}" 
                   class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-100 transition
                   {{ request()->routeIs('admin.destinasi.*') ? 'bg-blue-50 font-medium text-blue-700' : '' }}">
                    Destinations
                </a>
                <a href="{{ route('admin.kategori.index') }}" 
                   class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-100 transition
                   {{ request()->routeIs('admin.kategori.*') ? 'bg-blue-50 font-medium text-blue-700' : '' }}">
                    Categories
                </a>
            </div>
        </div>

        <!-- Manage Promotions -->
        <div x-data="{ openPromotions: {{ request()->routeIs('admin.promosi.*') || request()->routeIs('admin.transaksi.*') ? 'true' : 'false' }} }">
            <button @click="openPromotions = !openPromotions" 
                    class="w-full flex items-center justify-between px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-100 transition">
                <div class="flex items-center">
                    <i class="fas fa-bullhorn w-5"></i>
                    <span class="ml-3 truncate">Manage Promotions</span>
                </div>
                <i class="fas fa-chevron-down transition-transform" :class="openPromotions ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="openPromotions" x-cloak class="ml-6 mt-2 space-y-1">
                <a href="{{ route('admin.promosi.index') }}" 
                   class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-100 transition
                   {{ request()->routeIs('admin.promosi.*') ? 'bg-blue-50 font-medium text-blue-700' : '' }}">
                    Promotions / Ads
                </a>
                <a href="{{ route('admin.transaksi.index') }}" 
                   class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-100 transition
                   {{ request()->routeIs('admin.transaksi.*') ? 'bg-blue-50 font-medium text-blue-700' : '' }}">
                    Transactions
                </a>
            </div>
        </div>

        <!-- Manage Contact -->
        <a href="{{ route('admin.bantuan.index') }}" 
           class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-100 transition
           {{ request()->routeIs('admin.bantuan.*') ? 'bg-blue-200 text-blue-800 font-semibold' : '' }}">
            <i class="fas fa-life-ring w-5"></i>
            <span class="ml-3 truncate">Manage Contact</span>
        </a>

        <!-- Manage Edit Requests -->
        <a href="{{ route('admin.edit-requests.index') }}" 
           class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-100 transition
           {{ request()->routeIs('admin.edit-requests.*') ? 'bg-blue-200 text-blue-800 font-semibold' : '' }}">
            <i class="fas fa-pen-to-square w-5"></i>
            <span class="ml-3 truncate">Manage Edit Requests</span>
        </a>
    </nav>
</aside>

<!-- Overlay for mobile sidebar -->
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false"
     x-cloak
     x-transition.opacity
     class="fixed inset-0 bg-black bg-opacity-50 z-10 lg:hidden"></div>

<!-- Main Content -->
<main class="lg:ml-72 pt-16 min-h-screen bg-gray-100">
    <div class="p-6">
        @yield('content')
    </div>
</main>

@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Toggle profile modal password -->
<script>
    const toggleNewPassword = document.getElementById('toggleNewPassword');
    const newPassword = document.getElementById('newPassword');
    toggleNewPassword.addEventListener('click', () => {
        const type = newPassword.type === 'password' ? 'text' : 'password';
        newPassword.type = type;
        toggleNewPassword.classList.toggle('fa-eye-slash');
        toggleNewPassword.classList.toggle('fa-eye');
    });
</script>

</body>
</html>