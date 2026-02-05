@extends('layouts.pemilik')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-primary to-blue-400 px-8 py-6">
            <h1 class="text-3xl font-bold text-white">👤 Profil Saya</h1>
        </div>
        
        <div class="p-8">
            <div class="flex items-center gap-6 mb-8">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&size=120&background=9eccdb&color=fff" 
                     alt="Avatar" 
                     class="w-24 h-24 rounded-full border-4 border-primary">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ auth()->user()->name }}</h2>
                    <p class="text-gray-600">{{ auth()->user()->email }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Paket: <strong class="text-primary">{{ auth()->user()->currentPaket->nama_paket ?? 'Basic' }}</strong>
                    </p>
                </div>
            </div>
            
@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
    <i class="fas fa-check-circle mr-2"></i>
    {{ session('success') }}
</div>
@endif

            <form method="POST" action="{{ route('pemilik.profile.update') }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block font-semibold mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ auth()->user()->name }}" 
                           class="w-full px-4 py-3 border-2 rounded-lg">
                </div>
                
                <div>
                    <label class="block font-semibold mb-2">Email</label>
                    <input type="email" name="email" value="{{ auth()->user()->email }}" 
                           class="w-full px-4 py-3 border-2 rounded-lg">
                </div>
                
                <div>
                    <label class="block font-semibold mb-2">No. Telepon</label>
                    <input type="text" name="no_telepon" value="{{ auth()->user()->no_telepon }}" 
                           class="w-full px-4 py-3 border-2 rounded-lg">
                </div>
                
                <hr>
                
                <h3 class="font-bold text-lg">Ubah Password (opsional)</h3>
                
                <div>
                    <label class="block font-semibold mb-2">Password Lama</label>
                    <input type="password" name="current_password" 
                           class="w-full px-4 py-3 border-2 rounded-lg">
                </div>
                
                <div>
                    <label class="block font-semibold mb-2">Password Baru</label>
                    <input type="password" name="password" 
                           class="w-full px-4 py-3 border-2 rounded-lg">
                </div>
                
                <div>
                    <label class="block font-semibold mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" 
                           class="w-full px-4 py-3 border-2 rounded-lg">
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-primary to-blue-400 text-white px-6 py-4 rounded-lg font-semibold">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection