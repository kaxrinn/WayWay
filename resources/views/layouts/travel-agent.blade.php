<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - WayWay Travel Agent</title>

    <script src="https://cdn.tailwindcss.com"></script>
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
                    fontFamily: { sans: ['Poppins', 'sans-serif'] }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

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
    class="fixed top-20 right-6 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <i class="fas fa-check-circle mr-2"></i>
    {{ session('success') }}
</div>
@endif

<!-- Error Alert -->
@if(session('error'))
<div
    x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(() => show = false, 4000)"
    x-transition.opacity
    class="fixed top-20 right-6 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <i class="fas fa-exclamation-circle mr-2"></i>
    {{ session('error') }}
</div>
@endif

<!-- Navbar -->
<nav class="bg-gradient-to-r from-primary to-blue-400 shadow-lg fixed w-full z-30">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Logo & Hamburger -->
            <div class="flex items-center">
                <button @click="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar"
                        class="text-white p-2 rounded-md lg:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center ml-4 lg:ml-0">
                    <img src="{{ asset('assets/Logo/logo.png') }}" alt="WayWay Logo" class="h-10 w-10">
                    <span class="ml-3 font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#c6c4c9] via-[#415c7f] to-[#c6c4c9]">
                        WayWay
                    </span>
                </div>
            </div>

            <!-- User Menu -->
            <div class="flex items-center" x-data="{ userMenuOpen: false }">
                <button @click="userMenuOpen = !userMenuOpen"
                        class="flex items-center space-x-3 text-white hover:bg-white/20 rounded-full px-4 py-2 transition">
                    <img src="{{ auth()->user()->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=9eccdb&color=fff' }}"
                         alt="Profile"
                         class="h-8 w-8 rounded-full border-2 border-white">
                    <span class="font-medium hidden sm:inline">{{ auth()->user()->name }}</span>
                    <i class="fas fa-chevron-down text-sm"></i>
                </button>

                <!-- Dropdown -->
                <div x-show="userMenuOpen"
                     @click.away="userMenuOpen = false"
                     x-cloak
                     x-transition.opacity
                     x-transition.duration.200ms
                     class="absolute right-0 top-16 w-48 bg-white rounded-lg shadow-xl py-2 z-50">
                    <button @click="profileModal = true; userMenuOpen = false"
                            class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-accent transition">
                        <i class="fas fa-user-edit mr-2"></i> Edit Profile
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-4 py-2 text-gray-700 hover:bg-accent transition">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</nav>

<!-- Edit Profile Modal -->
<div x-show="profileModal" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
     role="dialog" aria-modal="true">
    <div @click.away="profileModal = false"
         class="bg-white rounded-xl w-full max-w-lg p-6 shadow-xl mx-4">

        <h2 class="text-xl font-bold mb-6 text-gray-800">Edit Profile</h2>

        <form method="POST" action="{{ route('travel-agent.profile.update') }}">
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
                        <input type="password" name="password" id="taPassword"
                               autocomplete="new-password" placeholder="Leave blank if unchanged"
                               class="w-full rounded-lg border-2 border-accent/70 bg-gray-50 px-4 py-2 focus:bg-white focus:border-accent focus:ring-2 focus:ring-accent/30 transition">
                        <i class="fas fa-eye-slash absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer text-gray-600"
                           id="toggleTaPassword"></i>
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                           autocomplete="new-password"
                           class="w-full rounded-lg border-2 border-accent/70 bg-gray-50 px-4 py-2 focus:bg-white focus:border-accent focus:ring-2 focus:ring-accent/30 transition">
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
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    <nav class="mt-5 px-2 space-y-1 pb-24 overflow-y-auto h-full">

        <!-- Dashboard -->
        <a href="{{ route('travel-agent.dashboard') }}"
           class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-100 transition
           {{ request()->routeIs('travel-agent.dashboard') ? 'bg-blue-200 text-blue-800 font-semibold' : '' }}">
            <i class="fas fa-home w-5"></i>
            <span class="ml-3 truncate">Dashboard</span>
        </a>

        <!-- My Travel Packages -->
        <a href="{{ route('travel-agent.packages.index') }}"
           class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-100 transition
           {{ request()->routeIs('travel-agent.packages.*') ? 'bg-blue-200 text-blue-800 font-semibold' : '' }}">
            <i class="fas fa-suitcase-rolling w-5"></i>
            <span class="ml-3 truncate">My Travel Packages</span>
        </a>

        <!-- My Active Package -->
        <a href="{{ route('travel-agent.subscriptions.index') }}"
           class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-100 transition
           {{ request()->routeIs('travel-agent.subscriptions.index') ? 'bg-blue-200 text-blue-800 font-semibold' : '' }}">
            <i class="fas fa-star w-5"></i>
            <span class="ml-3 truncate">My Active Package</span>
        </a>

        <!-- Upgrade Package -->
        <a href="{{ route('travel-agent.subscriptions.upgrade') }}"
           class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-100 transition
           {{ request()->routeIs('travel-agent.subscriptions.upgrade') ? 'bg-blue-200 text-blue-800 font-semibold' : '' }}">
            <i class="fas fa-arrow-up w-5"></i>
            <span class="ml-3 truncate">Upgrade Package</span>
        </a>

        <!-- Contact Admin -->
        <a href="{{ route('travel-agent.contact-admin') }}"
           class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-100 transition
           {{ request()->routeIs('travel-agent.contact-admin') ? 'bg-blue-200 text-blue-800 font-semibold' : '' }}">
            <i class="fas fa-phone w-5"></i>
            <span class="ml-3 truncate">Contact Admin</span>
        </a>

    </nav>

</aside>

<!-- Mobile Overlay -->
<div x-show="sidebarOpen"
     @click="sidebarOpen = false"
     x-cloak
     x-transition.opacity
     class="fixed inset-0 bg-black bg-opacity-50 z-10 lg:hidden"></div>

<!-- Main Content -->
<main class="lg:ml-72 pt-16 min-h-screen bg-gray-100">
    <div class="p-4 sm:p-6">

        @if ($errors->any())
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 5000)"
             x-transition.opacity
             class="fixed top-20 right-6 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <ul class="list-disc ml-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @yield('content')

    </div>
</main>

@stack('scripts')

<script>
    const toggleTaPassword = document.getElementById('toggleTaPassword');
    const taPassword = document.getElementById('taPassword');
    if (toggleTaPassword && taPassword) {
        toggleTaPassword.addEventListener('click', () => {
            const type = taPassword.type === 'password' ? 'text' : 'password';
            taPassword.type = type;
            toggleTaPassword.classList.toggle('fa-eye-slash');
            toggleTaPassword.classList.toggle('fa-eye');
        });
    }
</script>

</body>
</html>