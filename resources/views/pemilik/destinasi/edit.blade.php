@extends('layouts.pemilik')

@section('title', 'Edit Destinasi')

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
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-8 py-6">
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-edit"></i>
                Edit Destinasi Wisata
            </h1>
            <p class="text-white/90 mt-2">Edit: <strong>{{ $destinasi->nama_destinasi }}</strong></p>
        </div>
        
        <!-- Form -->
        <form method="POST" action="{{ route('pemilik.destinasi.update', $destinasi->id) }}" enctype="multipart/form-data" class="p-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Destinasi -->
                <div class="md:col-span-2">
                    <label for="nama_destinasi" class="block text-gray-700 font-semibold mb-2">
                        Nama Destinasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="nama_destinasi" 
                           name="nama_destinasi" 
                           value="{{ old('nama_destinasi', $destinasi->nama_destinasi) }}"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('nama_destinasi') border-red-500 @enderror"
                           required>
                    @error('nama_destinasi')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
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
                        @foreach($kategori as $kategori)
                            <option value="{{ $kategori->id }}" 
                                    {{ old('kategori_id', $destinasi->kategori_id) == $kategori->id ? 'selected' : '' }}>
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
                        Harga (Rp) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="number" 
                               id="harga" 
                               name="harga" 
                               value="{{ old('harga', $destinasi->harga) }}"
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
                           value="{{ old('latitude', $destinasi->latitude) }}"
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
                           value="{{ old('longitude', $destinasi->longitude) }}"
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
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('deskripsi') border-red-500 @enderror"
                              required>{{ old('deskripsi', $destinasi->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Foto Saat Ini -->
                @if($destinasi->foto && count($destinasi->foto) > 0)
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">Foto Saat Ini</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($destinasi->foto as $index => $foto)
                        <div class="relative group">
                            <img src="{{ Storage::url($foto) }}" 
                                 alt="Foto {{ $index + 1 }}"
                                 class="w-full h-32 object-cover rounded-lg shadow">
                            <label class="absolute top-2 right-2 cursor-pointer">
                                <input type="checkbox" 
                                       name="delete_foto[]" 
                                       value="{{ $foto }}"
                                       class="w-5 h-5">
                                <span class="absolute inset-0 bg-red-500 text-white flex items-center justify-center rounded opacity-0 group-hover:opacity-90 transition text-xs font-bold">
                                    Hapus
                                </span>
                            </label>
                            <div class="absolute bottom-2 left-2 bg-black/50 text-white px-2 py-1 rounded text-xs">
                                Foto {{ $index + 1 }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Centang foto yang ingin dihapus</p>
                </div>
                @endif
                
                <!-- Upload Foto Baru -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Tambah Foto Baru (Sisa slot: {{ max(0, ($limits['max_foto'] ?? 999) - count($destinasi->foto ?? [])) }})
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-primary transition">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-3"></i>
                            <p class="text-gray-600 mb-2">Upload foto tambahan</p>
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
                </div>
                
                <!-- Video Saat Ini -->
                @if($destinasi->video && count($destinasi->video) > 0)
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">Video Saat Ini</label>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($destinasi->video as $index => $video)
                        <div class="relative group bg-gray-100 rounded-lg p-4">
                            <video src="{{ Storage::url($video) }}" 
                                   class="w-full h-32 object-cover rounded"
                                   controls></video>
                            <label class="absolute top-2 right-2 cursor-pointer">
                                <input type="checkbox" 
                                       name="delete_video[]" 
                                       value="{{ $video }}"
                                       class="w-5 h-5">
                            </label>
                            <p class="text-xs text-gray-600 mt-2">Video {{ $index + 1 }}</p>
                        </div>
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Centang video yang ingin dihapus</p>
                </div>
                @endif
                
                <!-- Upload Video Baru -->
                @if(($limits['max_video'] ?? 0) > 0)
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Tambah Video Baru (Sisa slot: {{ max(0, ($limits['max_video'] ?? 0) - count($destinasi->video ?? [])) }})
                    </label>
                    <div class="border-2 border-dashed border-purple-300 rounded-lg p-6 hover:border-purple-500 transition">
                        <div class="text-center">
                            <i class="fas fa-video text-5xl text-purple-400 mb-3"></i>
                            <p class="text-gray-600 mb-2">Upload video tambahan</p>
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
                </div>
                @endif
                
                <!-- Featured (Premium Only) -->
                @if($limits['is_featured_allowed'])
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer p-4 bg-yellow-50 border-2 border-yellow-200 rounded-lg hover:bg-yellow-100 transition">
                        <input type="checkbox" 
                               name="is_featured" 
                               value="1"
                               {{ $destinasi->is_featured ? 'checked' : '' }}
                               class="w-6 h-6 text-yellow-500">
                        <div>
                            <p class="font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-star text-yellow-500"></i>
                                Jadikan Destinasi Featured
                            </p>
                            <p class="text-sm text-gray-600">Destinasi akan tampil di homepage dengan highlight khusus</p>
                        </div>
                    </label>
                </div>
                @endif
            </div>
            
            <!-- Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white px-6 py-4 rounded-lg transition shadow-lg hover:shadow-xl font-semibold text-lg flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    Update Destinasi
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
let currentFotoCount = {{ count($destinasi->foto ?? []) }};
let currentVideoCount = {{ count($destinasi->video ?? []) }};
let maxFoto = {{ $limits['max_foto'] ?? 999 }};
let maxVideo = {{ $limits['max_video'] ?? 0 }};

function previewFoto(input) {
    const preview = document.getElementById('fotoPreview');
    preview.innerHTML = '';
    
    const availableSlots = maxFoto - currentFotoCount;
    if (input.files.length > availableSlots) {
        alert(`Hanya bisa tambah ${availableSlots} foto lagi!`);
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
                <div class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded text-xs font-bold">
                    Baru
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
    
    const availableSlots = maxVideo - currentVideoCount;
    if (input.files.length > availableSlots) {
        alert(`Hanya bisa tambah ${availableSlots} video lagi!`);
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
            <span class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded text-xs">Baru</span>
        `;
        preview.appendChild(div);
    });
}
</script>
@endpush