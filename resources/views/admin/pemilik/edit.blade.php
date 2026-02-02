@extends('layouts.admin')

@section('title', 'Edit Pemilik Wisata')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Back Button -->
    <a href="{{ route('admin.pemilik.index') }}" 
       class="inline-flex items-center text-gray-600 hover:text-primary transition mb-6">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar
    </a>
    
    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-8 py-6">
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-user-edit"></i>
                Edit Pemilik Wisata
            </h1>
            <p class="text-white/90 mt-2">Edit data pemilik wisata: <strong>{{ $pemilik->name }}</strong></p>
        </div>
        
        <!-- Form -->
        <form method="POST" action="{{ route('admin.pemilik.update', $pemilik->id) }}" class="p-8">
            @csrf
            @method('PUT')
            
            <!-- Nama Lengkap -->
            <div class="mb-6">
                <label for="name" class="block text-gray-700 font-semibold mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $pemilik->name) }}"
                       placeholder="Masukkan nama lengkap"
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('name') border-red-500 @enderror"
                       required>
                @error('name')
                    <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>
            
            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-gray-700 font-semibold mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', $pemilik->email) }}"
                       placeholder="email@example.com"
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('email') border-red-500 @enderror"
                       required>
                @error('email')
                    <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>
            
            <!-- No Telepon -->
            <div class="mb-6">
                <label for="no_telepon" class="block text-gray-700 font-semibold mb-2">
                    No. Telepon
                </label>
                <input type="text" 
                       id="no_telepon" 
                       name="no_telepon" 
                       value="{{ old('no_telepon', $pemilik->no_telepon) }}"
                       placeholder="08123456789"
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('no_telepon') border-red-500 @enderror">
                @error('no_telepon')
                    <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>
            
            <!-- Destinasi -->
            <div class="mb-6">
                <label for="destinasi_id" class="block text-gray-700 font-semibold mb-2">
                    Pilih Destinasi <span class="text-red-500">*</span>
                </label>
                <select name="destinasi_id" required class="w-full px-4 py-3 border-2 rounded-lg">
    <option value="">-- Pilih Destinasi --</option>
    @foreach($destinasi as $d)
        <option value="{{ $d->id }}"
            {{ $d->user_id == $pemilik->id ? 'selected' : '' }}>
            {{ $d->nama_destinasi }}
        </option>
    @endforeach
</select>

                    <option value="">-- Pilih Destinasi --</option>
                    @foreach($destinasi as $destinasi)
                        <option value="{{ $destinasi->id }}" 
                                {{ old('destinasi_id', $pemilik->destinasi_id) == $destinasi->id ? 'selected' : '' }}>
                            {{ $destinasi->nama_destinasi }}
                        </option>
                    @endforeach
                </select>
                @error('destinasi_id')
                    <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>
            
            <!-- Info Box -->
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg mb-6">
                <p class="font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-info-circle text-yellow-600"></i>
                    Password bersifat opsional
                </p>
                <p class="text-sm text-gray-600 mt-1">Kosongkan field password jika tidak ingin mengubah password</p>
            </div>
            
            <!-- Password Baru -->
            <div class="mb-6" x-data="{ showPassword: false }">
                <label for="password" class="block text-gray-700 font-semibold mb-2">
                    Password Baru (Opsional)
                </label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" 
                           id="password" 
                           name="password"
                           placeholder="Kosongkan jika tidak ingin mengubah"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition pr-12 @error('password') border-red-500 @enderror">
                    <button type="button" 
                            @click="showPassword = !showPassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-primary transition">
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>
            
            <!-- Konfirmasi Password Baru -->
            <div class="mb-6" x-data="{ showPassword: false }">
                <label for="password_confirmation" class="block text-gray-700 font-semibold mb-2">
                    Konfirmasi Password Baru
                </label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" 
                           id="password_confirmation" 
                           name="password_confirmation"
                           placeholder="Ulangi password baru"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition pr-12">
                    <button type="button" 
                            @click="showPassword = !showPassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-primary transition">
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>
            </div>
            
            <!-- Password Requirements Info -->
            <div class="bg-gray-50 border-l-4 border-gray-400 p-4 rounded-lg mb-6">
                <p class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-info-circle text-gray-500"></i>
                    Jika mengubah password:
                </p>
                <ul class="text-sm text-gray-600 space-y-1 ml-6">
                    <li class="flex items-center gap-2">
                        <i class="fas fa-check text-green-500"></i>
                        Minimal 8 karakter
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-check text-green-500"></i>
                        Kombinasi huruf dan angka
                    </li>
                </ul>
            </div>
            
            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white px-6 py-4 rounded-lg transition shadow-lg hover:shadow-xl font-semibold text-lg flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    Update Pemilik Wisata
                </button>
                <a href="{{ route('admin.pemilik.index') }}" 
                   class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-4 rounded-lg transition shadow-lg hover:shadow-xl font-semibold text-lg flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection