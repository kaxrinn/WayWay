@extends('layouts.admin')

@section('title', 'Add Destination')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <a href="{{ route('admin.destinasi.index') }}" 
       class="inline-flex items-center text-gray-600 hover:text-primary transition mb-6">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to List
    </a>
    
    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary to-blue-400 px-8 py-6">
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-plus-circle"></i>
                Add Tourist Destination
            </h1>
            <p class="text-white/90 mt-2">Fill out the form below to add a new tourist destination</p>
        </div>
        
        <!-- Form -->
        <form method="POST" action="{{ route('admin.destinasi.store') }}" enctype="multipart/form-data" class="p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Destinasi -->
                <div class="md:col-span-2">
                    <label for="nama_destinasi" class="block text-gray-700 font-semibold mb-2">
                        Destination Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="nama_destinasi" 
                           name="nama_destinasi" 
                           value="{{ old('nama_destinasi') }}"
                           placeholder="Example: Kuta Beach Bali"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('nama_destinasi') border-red-500 @enderror"
                           required>
                    @error('nama_destinasi')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Kategori -->
                <div>
                    <label for="kategori_id" class="block text-gray-700 font-semibold mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="kategori_id" 
                            name="kategori_id"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('kategori_id') border-red-500 @enderror"
                            required>
                        <option value="">-- Select Category --</option>
                        @foreach($kategori as $kat)
                            <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
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
                        Price (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="harga" 
                           name="harga" 
                           value="{{ old('harga') }}"
                           placeholder="0"
                           min="0"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('harga') border-red-500 @enderror"
                           required>
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
                           placeholder="Example: -8.7184"
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
                           placeholder="Example: 115.1686"
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
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea id="deskripsi" 
                              name="deskripsi" 
                              rows="4"
                              placeholder="Describe this destination..."
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition @error('deskripsi') border-red-500 @enderror"
                              required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Foto Baru -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Upload Destination Photos
                    </label>

                    <input type="file" name="foto[]" multiple accept="image/*"
                        class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg"
                        onchange="previewImages(this)">

                    <ul class="text-sm text-gray-500 mt-2">
                        <li>• Leave empty if you don't want to add photos</li>
                        <li>• Maximum 3 photos upload</li>
                        <li>• Accepted formats: JPG, PNG, JPEG, WEBP</li>
                        <li>• Maximum size per photo: 2MB</li>
                    </ul>

                    <div id="imagePreview" class="mt-4 hidden">
                        <p class="text-sm font-semibold text-gray-700 mb-2">Photo Preview:</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="previewContainer"></div>
                    </div>

                    @error('foto')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Info Box -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mt-6">
                <p class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Coordinate Tips:
                </p>
                <ul class="text-sm text-gray-600 space-y-1 ml-6">
                    <li>• Use Google Maps to get accurate coordinates</li>
                    <li>• Right click on location → "What's here?" → Copy coordinates</li>
                    <li>• Format: Latitude (example: -8.7184), Longitude (example: 115.1686)</li>
                </ul>
            </div>
            
            <!-- Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-primary to-blue-400 hover:from-blue-400 hover:to-primary text-white px-6 py-4 rounded-lg transition shadow-lg hover:shadow-xl font-semibold text-lg flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    Save Destination
                </button>
                <a href="{{ route('admin.destinasi.index') }}" 
                   class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-4 rounded-lg transition shadow-lg hover:shadow-xl font-semibold text-lg flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImages(input) {
    const preview = document.getElementById('imagePreview');
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';
    preview.classList.remove('hidden');

    if (input.files.length > 3) {
        alert('Maximum 3 photos allowed');
        input.value = '';
        preview.classList.add('hidden');
        return;
    }

    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = "w-full h-48 object-cover rounded-lg shadow";
            container.appendChild(img);
        }
        reader.readAsDataURL(file);
    });
}
</script>
@endpush