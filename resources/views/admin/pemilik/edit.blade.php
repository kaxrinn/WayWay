@extends('layouts.admin')

@section('title', 'Edit Tourism Owner')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Back Button -->
    <a href="{{ route('admin.pemilik.index') }}" 
       class="inline-flex items-center text-gray-600 hover:text-primary transition mb-6">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to List
    </a>
    
    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 sm:px-8 py-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
            <i class="fas fa-user-edit"></i>
            Edit Tourism Owner
        </h1>
        <p class="text-white/90 mt-2 text-sm sm:text-base">
            Edit tourism owner data: 
            <strong>{{ $pemilik->name }}</strong>
        </p>
    </div>
    
    <!-- Form -->
    <form method="POST" action="{{ route('admin.pemilik.update', $pemilik->id) }}" 
          class="p-6 sm:p-8">
        @csrf
        @method('PUT')

        <!-- GRID START -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Full Name -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    name="name"
                    value="{{ old('name', $pemilik->name) }}"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('name') border-red-500 @enderror"
                    required>
                @error('name')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email"
                    name="email"
                    value="{{ old('email', $pemilik->email) }}"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('email') border-red-500 @enderror"
                    required>
                @error('email')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

        </div>
        <!-- GRID END -->


        <!-- Phone -->
        <div class="mt-6">
            <label class="block text-gray-700 font-semibold mb-2">
                Phone Number
            </label>
            <input type="text"
                name="no_telepon"
                value="{{ old('no_telepon', $pemilik->no_telepon) }}"
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition">
        </div>


        <!-- Destination -->
        <div class="mt-6">
            <label class="block text-gray-700 font-semibold mb-2">
                Select Destination <span class="text-red-500">*</span>
            </label>
            <select name="destinasi_id"
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition"
                required>
                <option value="">-- Select Destination --</option>
                @foreach($destinasi as $d)
                    <option value="{{ $d->id }}"
                        {{ $d->user_id == $pemilik->id ? 'selected' : '' }}>
                        {{ $d->nama_destinasi }}
                    </option>
                @endforeach
            </select>
        </div>


        <!-- Info Box -->
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg mt-6">
            <p class="font-semibold text-gray-700">
                Password is optional
            </p>
            <p class="text-sm text-gray-600 mt-1">
                Leave empty if you don't want to change it
            </p>
        </div>


        <!-- Password Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

            <!-- New Password -->
            <div x-data="{ showPassword: false }">
                <label class="block text-gray-700 font-semibold mb-2">
                    New Password
                </label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'"
                        name="password"
                        autocomplete="new-password"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition pr-12">
                    <button type="button"
                        @click="showPassword = !showPassword"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>
            </div>

            <!-- Confirm Password -->
            <div x-data="{ showPassword: false }">
                <label class="block text-gray-700 font-semibold mb-2">
                    Confirm Password
                </label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'"
                        name="password_confirmation"
                        autocomplete="new-password"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition pr-12">
                    <button type="button"
                        @click="showPassword = !showPassword"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>
            </div>


        <!-- Password Requirements Info -->
            <div class="bg-gray-50 border-l-4 border-gray-400 p-4 rounded-lg mb-6 md:col-span-2">
                <p class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-info-circle text-gray-700"></i>
                    If changing password:
                </p>
                <ul class="text-sm text-gray-600 space-y-1 ml-6">
                    <li class="flex items-center gap-2">
                        <i class="fas fa-check text-green-500"></i>
                        Minimum 8 characters
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-check text-green-500"></i>
                        Combination of letters and numbers
                    </li>
                </ul>
            </div>

 </div>
        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 mt-8">
            <button type="submit"
                class="w-full sm:flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white px-6 py-4 rounded-lg transition shadow-lg font-semibold text-lg flex items-center justify-center gap-2">
                <i class="fas fa-save"></i>
                Update
            </button>

            <a href="{{ route('admin.pemilik.index') }}"
                class="w-full sm:flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-4 rounded-lg transition shadow-lg font-semibold text-lg flex items-center justify-center gap-2">
                <i class="fas fa-times"></i>
                Cancel
            </a>
        </div>

    </form>
</div>
@endsection
