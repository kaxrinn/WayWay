@extends('layouts.admin')

@section('title', 'Edit Destinasi')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <a href="{{ route('admin.destinasi.index') }}" 
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
            <p class="text-white/90 mt-2">Edit data: <strong>{{ $destinasi->nama_destinasi }}</strong></p>
        </div>
        
        <!-- Form -->
        <form method="POST" action="{{ route('admin.destinasi.update', $destinasi->id) }}" enctype="multipart/form-data" class="p-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Nama Destinasi -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">Nama Destinasi *</label>
                    <input type="text" name="nama_destinasi" value="{{ old('nama_destinasi',$destinasi->nama_destinasi) }}"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg" required>
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Kategori *</label>
                    <select name="kategori_id" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg" required>
                        @foreach($kategori as $kat)
                        <option value="{{ $kat->id }}" {{ $destinasi->kategori_id==$kat->id?'selected':'' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Harga -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Harga *</label>
                    <input type="number" name="harga" value="{{ old('harga',$destinasi->harga) }}"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg" required>
                </div>

                <!-- Latitude -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Latitude *</label>
                    <input type="text" name="latitude" value="{{ old('latitude',$destinasi->latitude) }}"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg" required>
                </div>

                <!-- Longitude -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Longitude *</label>
                    <input type="text" name="longitude" value="{{ old('longitude',$destinasi->longitude) }}"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg" required>
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">Deskripsi *</label>
                    <textarea name="deskripsi" rows="4"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg" required>{{ old('deskripsi',$destinasi->deskripsi) }}</textarea>
                </div>

                <!-- FOTO SAAT INI (MAX 3) -->
                @if($destinasi->foto)
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">Foto Saat Ini</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach(json_decode($destinasi->foto) as $foto)
                            <img src="{{ Storage::url($foto) }}"
                                class="w-full h-48 object-cover rounded-lg shadow">
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- FOTO BARU -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Ganti Foto (Maks 3)
                    </label>

                    <input type="file" name="foto[]" multiple accept="image/*"
                        class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg"
                        onchange="previewImage(this)">

                    <p class="text-sm text-gray-500 mt-2">Kosongkan jika tidak ingin mengubah foto</p>

                    <div id="imagePreview" class="mt-4 hidden">
                        <p class="text-sm font-semibold text-gray-700 mb-2">Preview Foto Baru:</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="previewContainer"></div>
                    </div>

                    @error('foto')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-6 py-4 rounded-lg font-semibold">
                    <i class="fas fa-save"></i> Update Destinasi
                </button>
                <a href="{{ route('admin.destinasi.index') }}" 
                   class="flex-1 bg-gray-500 text-white px-6 py-4 rounded-lg font-semibold text-center">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';
    preview.classList.remove('hidden');

    if (input.files.length > 3) {
        alert('Maksimal upload 3 foto');
        input.value = '';
        preview.classList.add('hidden');
        return;
    }

    for (let i = 0; i < input.files.length; i++) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = "w-full h-48 object-cover rounded-lg shadow";
            container.appendChild(img);
        }
        reader.readAsDataURL(input.files[i]);
    }
}
</script>
@endpush
