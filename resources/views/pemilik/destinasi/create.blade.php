@extends('layouts.pemilik')

@section('title', 'Tambah Destinasi')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <a href="{{ route('pemilik.destinasi.index') }}" 
       class="inline-flex items-center text-gray-600 hover:text-primary transition mb-6">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar
    </a>
    
    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary to-blue-400 px-8 py-6">
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-plus-circle"></i>
                Tambah Destinasi Wisata Baru
            </h1>
            <p class="text-white/90 mt-2">Isi form di bawah untuk menambahkan destinasi wisata</p>
        </div>
        
        <!-- Paket Info -->
        <div class="bg-blue-50 px-8 py-4 border-b border-blue-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <i class="fas fa-info-circle text-blue-500 text-2xl"></i>
                    <div>
                        <p class="text-sm text-gray-600">Batasan Paket {{ $limits['max_foto'] ?? '∞' }}</p>
                        <p class="font-semibold text-gray-800">
                            Max {{ $limits['max_foto'] ?? '∞' }} foto & {{ $limits['max_video'] ?? '∞' }} video per destinasi
                        </p>
                    </div>
                </div>
                <a href="{{ route('pemilik.paket.index') }}" 
                   class="text-primary hover:text-blue-600 font-semibold text-sm">
                    Upgrade Paket <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        
        <!-- Form -->
        <form method="POST" action="{{ route('pemilik.destinasi.store') }}" enctype="multipart/form-data" class="p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Destinasi -->
                <div class="md:col-span-2">
                    <label for="nama_destinasi" class="block text-gray-700 font-semibold mb-2">
                        Nama Destinasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="nama_destinasi" 
                           name="nama_destinasi" 
                           value="{{ old('nama_destinasi') }}"
                           placeholder="Contoh: Pantai Kuta Bali"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('nama_destinasi') border-red-500 @enderror"
                           required>
                    @error('nama_destinasi')
                        <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Kategori -->
                <div>
                    <label for="kategori_id" class="block text-gray-700 font-semibold mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="kategori_id" 
                            name="kategori_id"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('kategori_id') border-red-500 @enderror"
                            required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Harga -->
                <div>
                    <label for="harga" class="block text-gray-700 font-semibold mb-2">
                        Harga Tiket (Rp) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="number" 
                               id="harga" 
                               name="harga" 
                               value="{{ old('harga') }}"
                               placeholder="0"
                               min="0"
                               class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('harga') border-red-500 @enderror"
                               required>
                    </div>
                    @error('harga')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Latitude -->
                <div>
                    <label for="latitude" class="block text-gray-700 font-semibold mb-2">
                        Latitude <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="latitude" 
                           name="latitude" 
                           value="{{ old('latitude') }}"
                           placeholder="Contoh: -8.7184"
                           step="any"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('latitude') border-red-500 @enderror"
                           required>
                    @error('latitude')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Longitude -->
                <div>
                    <label for="longitude" class="block text-gray-700 font-semibold mb-2">
                        Longitude <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="longitude" 
                           name="longitude" 
                           value="{{ old('longitude') }}"
                           placeholder="Contoh: 115.1686"
                           step="any"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('longitude') border-red-500 @enderror"
                           required>
                    @error('longitude')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label for="deskripsi" class="block text-gray-700 font-semibold mb-2">
                        Deskripsi <span class="text-red-500">*</span>
                    </label>
                    <textarea id="deskripsi" 
                              name="deskripsi" 
                              rows="4"
                              placeholder="Jelaskan tentang destinasi ini..."
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('deskripsi') border-red-500 @enderror"
                              required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Foto Upload -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Foto Destinasi (Max {{ $limits['max_foto'] ?? '∞' }} foto)
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-primary transition">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-3"></i>
                            <p class="text-gray-600 mb-2">Klik atau drag & drop foto di sini</p>
                            <p class="text-sm text-gray-500">Format: JPG, PNG. Maksimal 2MB per foto</p>
                            <input type="file" 
                                   id="foto" 
                                   name="foto[]" 
                                   accept="image/*"
                                   multiple
                                   class="hidden"
                                   onchange="previewFoto(this)">
                            <button type="button" 
                                    onclick="document.getElementById('foto').click()"
                                    class="mt-4 bg-primary hover:bg-blue-500 text-white px-6 py-2 rounded-lg transition">
                                <i class="fas fa-images mr-2"></i>
                                Pilih Foto
                            </button>
                        </div>
                    </div>
                    <div id="fotoPreview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>
                    @error('foto.*')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Video Upload -->
                @if(($limits['max_video'] ?? 0) > 0)
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Video Destinasi (Max {{ $limits['max_video'] }} video)
                    </label>
                    <div class="border-2 border-dashed border-purple-300 rounded-lg p-6 hover:border-purple-500 transition">
                        <div class="text-center">
                            <i class="fas fa-video text-5xl text-purple-400 mb-3"></i>
                            <p class="text-gray-600 mb-2">Klik atau drag & drop video di sini</p>
                            <p class="text-sm text-gray-500">Format: MP4, MOV, AVI. Maksimal 10MB per video</p>
                            <input type="file" 
                                   id="video" 
                                   name="video[]" 
                                   accept="video/*"
                                   multiple
                                   class="hidden"
                                   onchange="previewVideo(this)">
                            <button type="button" 
                                    onclick="document.getElementById('video').click()"
                                    class="mt-4 bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded-lg transition">
                                <i class="fas fa-video mr-2"></i>
                                Pilih Video
                            </button>
                        </div>
                    </div>
                    <div id="videoPreview" class="grid grid-cols-2 gap-4 mt-4"></div>
                    @error('video.*')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                @endif
            </div>
            
            <!-- Info Box -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mt-6">
                <p class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Tips Koordinat:
                </p>
                <ul class="text-sm text-gray-600 space-y-1 ml-6">
                    <li>• Gunakan Google Maps untuk mendapatkan koordinat yang akurat</li>
                    <li>• Klik kanan pada lokasi → "What's here?" → Copy koordinat</li>
                    <li>• Format: Latitude (contoh: -8.7184), Longitude (contoh: 115.1686)</li>
                </ul>
            </div>
            
            <!-- Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-primary to-blue-400 hover:from-blue-400 hover:to-primary text-white px-6 py-4 rounded-lg transition shadow-lg hover:shadow-xl font-semibold text-lg flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    Simpan Destinasi
                </button>
                <a href="{{ route('pemilik.destinasi.index') }}" 
                   class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-4 rounded-lg transition shadow-lg hover:shadow-xl font-semibold text-lg flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let maxFoto = {{ $limits['max_foto'] ?? 999 }};
let maxVideo = {{ $limits['max_video'] ?? 0 }};

function previewFoto(input) {
    const preview = document.getElementById('fotoPreview');
    preview.innerHTML = '';
    
    if (input.files.length > maxFoto) {
        alert(`Maksimal ${maxFoto} foto untuk paket Anda!`);
        input.value = '';
        return;
    }
    
    Array.from(input.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg shadow">
                <div class="absolute top-2 right-2 bg-primary text-white px-2 py-1 rounded text-xs font-bold">
                    ${index + 1}
                </div>
            `;
            preview.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
}

function previewVideo(input) {
    const preview = document.getElementById('videoPreview');
    preview.innerHTML = '';
    
    if (input.files.length > maxVideo) {
        alert(`Maksimal ${maxVideo} video untuk paket Anda!`);
        input.value = '';
        return;
    }
    
    Array.from(input.files).forEach((file, index) => {
        const div = document.createElement('div');
        div.className = 'relative bg-gray-100 rounded-lg p-4';
        div.innerHTML = `
            <i class="fas fa-video text-4xl text-purple-500"></i>
            <p class="text-sm text-gray-700 mt-2">${file.name}</p>
            <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
        `;
        preview.appendChild(div);
    });
}
</script>
@endpush