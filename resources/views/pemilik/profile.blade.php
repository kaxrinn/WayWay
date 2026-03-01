@extends('layouts.pemilik')

@section('title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-primary to-blue-400 px-5 sm:px-8 py-5 sm:py-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-white">👤 My Profile</h1>
        </div>

        <div class="p-5 sm:p-8">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-6 mb-8">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&size=120&background=9eccdb&color=fff"
                     alt="Avatar"
                     class="w-20 h-20 sm:w-24 sm:h-24 rounded-full border-4 border-primary flex-shrink-0">
                <div class="text-center sm:text-left">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ auth()->user()->name }}</h2>
                    <p class="text-gray-600 text-sm sm:text-base">{{ auth()->user()->email }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Package: <strong class="text-primary">{{ auth()->user()->currentPaket->nama_paket ?? 'Basic' }}</strong>
                    </p>
                </div>
            </div>

            @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 text-sm sm:text-base">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('pemilik.profile.update') }}" class="space-y-5 sm:space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-semibold mb-2 text-sm sm:text-base">Full Name</label>
                    <input type="text" name="name" value="{{ auth()->user()->name }}"
                           class="w-full px-4 py-3 border-2 rounded-lg text-sm sm:text-base">
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-sm sm:text-base">Email</label>
                    <input type="email" name="email" value="{{ auth()->user()->email }}"
                           class="w-full px-4 py-3 border-2 rounded-lg text-sm sm:text-base">
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-sm sm:text-base">Phone Number</label>
                    <input type="text" name="no_telepon" value="{{ auth()->user()->no_telepon }}"
                           class="w-full px-4 py-3 border-2 rounded-lg text-sm sm:text-base">
                </div>

                <hr>

                <h3 class="font-bold text-base sm:text-lg">Change Password <span class="font-normal text-gray-400 text-sm">(optional)</span></h3>

                <div>
                    <label class="block font-semibold mb-2 text-sm sm:text-base">Current Password</label>
                    <input type="password" name="current_password"
                           class="w-full px-4 py-3 border-2 rounded-lg text-sm sm:text-base">
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-sm sm:text-base">New Password</label>
                    <input type="password" name="password"
                           class="w-full px-4 py-3 border-2 rounded-lg text-sm sm:text-base">
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-sm sm:text-base">Confirm New Password</label>
                    <input type="password" name="password_confirmation"
                           class="w-full px-4 py-3 border-2 rounded-lg text-sm sm:text-base">
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-primary to-blue-400 text-white px-6 py-3 sm:py-4 rounded-lg font-semibold text-sm sm:text-base">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                    <a href="{{ route('pemilik.dashboard') }}"
                       class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-600 px-6 py-3 sm:py-4 rounded-lg font-semibold text-sm sm:text-base text-center transition">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection