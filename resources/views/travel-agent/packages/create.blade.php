@extends('layouts.travel-agent')

@section('title', 'Upload New Package')

@section('content')
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-8 py-6">
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-plus-circle"></i>
                Upload new package
            </h1>
            <p class="text-blue-100 mt-2">Create new travel packages to offer to tourists</p>
        </div>
        
        <!-- Form -->
        <form method="POST" action="{{ route('travel-agent.packages.store') }}" enctype="multipart/form-data" class="p-8">
            @csrf
            
            <div class="space-y-8">
                <!-- INFORMASI DASAR -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-blue-500">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Basic Information
                    </h3>

                    <div class="space-y-4">
                        <!-- Nama Paket -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Name Package *</label>
                            <input type="text" name="nama_paket" value="{{ old('nama_paket') }}" placeholder="Contoh: Batam 3D2N Adventure" required
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition @error('nama_paket') border-red-500 @enderror">
                            @error('nama_paket')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Package Description *</label>
                            <textarea name="deskripsi" rows="4" placeholder="Jelaskan keunikan dan highlight paket wisata Anda..." required
                                      class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Thumbnail -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Banner</label>
                            <input type="file" name="thumbnail" accept="image/*"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 @error('thumbnail') border-red-500 @enderror">
                            <p class="text-gray-500 text-xs mt-1">Format: JPG, PNG | Max 2MB</p>
                            @error('thumbnail')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <!-- Harga -->
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Price per Person (IDR) *</label>
                                <input type="number" name="harga_per_orang" value="{{ old('harga_per_orang') }}" min="0" step="1000" required
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition @error('harga_per_orang') border-red-500 @enderror">
                                @error('harga_per_orang')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Durasi -->
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Duration (Days) *</label>
                                <input type="number" name="durasi_hari" value="{{ old('durasi_hari') }}" min="1" required
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition @error('durasi_hari') border-red-500 @enderror">
                                @error('durasi_hari')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Berangkat -->
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Departure Date *</label>
                                <input type="date" name="tanggal_keberangkatan" value="{{ old('tanggal_keberangkatan') }}" required
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition @error('tanggal_keberangkatan') border-red-500 @enderror">
                                @error('tanggal_keberangkatan')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DESTINASI -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-green-500">
                        <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>
                        Destinations to Visit
                    </h3>

                    <div id="destinasi-container" class="space-y-2">
                        <input type="text" name="destinasi[]" placeholder="Contoh: Jembatan Barelang" value="{{ old('destinasi.0') }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition">
                    </div>
                    <button type="button" onclick="addDestinasiField()" class="mt-3 text-green-600 hover:text-green-800 font-semibold text-sm">
                        <i class="fas fa-plus mr-1"></i> Add Destination
                    </button>
                    @error('destinasi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- FASILITAS INCLUDE -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-blue-500">
                        <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                        Facilities that are Included
                    </h3>

                    <div id="include-container" class="space-y-2">
                        <input type="text" name="fasilitas_include[]" placeholder="Contoh: Transportasi" value="{{ old('fasilitas_include.0') }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                    </div>
                    <button type="button" onclick="addIncludeField()" class="mt-3 text-blue-600 hover:text-blue-800 font-semibold text-sm">
                        <i class="fas fa-plus mr-1"></i> Add Facility
                    </button>
                </div>

                <!-- FASILITAS EXCLUDE -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-red-500">
                        <i class="fas fa-times-circle text-red-500 mr-2"></i>
                        Facilities that are NOT Included
                    </h3>

                    <div id="exclude-container" class="space-y-2">
                        <input type="text" name="fasilitas_exclude[]" placeholder="Example: homestay" value="{{ old('fasilitas_exclude.0') }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 transition">
                    </div>
                    <button type="button" onclick="addExcludeField()" class="mt-3 text-red-600 hover:text-red-800 font-semibold text-sm">
                        <i class="fas fa-plus mr-1"></i> Add Facility
                    </button>
                </div>

                <!-- PESERTA -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-purple-500">
                        <i class="fas fa-users text-purple-500 mr-2"></i>
                        Information about Participants
                    </h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Minimum Participants *</label>
                            <input type="number" name="min_peserta" value="{{ old('min_peserta', 1) }}" min="1" required
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('min_peserta') border-red-500 @enderror">
                            @error('min_peserta')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Maximum Participants *</label>
                            <input type="number" name="max_peserta" value="{{ old('max_peserta', 20) }}" min="1" required
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('max_peserta') border-red-500 @enderror">
                            @error('max_peserta')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-gray-700 font-semibold mb-2">Meeting Point *</label>
                        <input type="text" name="meeting_point" value="{{ old('meeting_point') }}" placeholder="Contoh: Batam Centre Ferry Terminal" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('meeting_point') border-red-500 @enderror">
                        @error('meeting_point')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- KONTAK -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-orange-500">
                        <i class="fas fa-phone text-orange-500 mr-2"></i>
                        Kontak Anda
                    </h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">WhatsApp</label>
                            <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="08xxxxxxxxxx"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Contoh: agen@email.com"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Instagram</label>
                            <input type="text" name="instagram" value="{{ old('instagram') }}" placeholder="@username"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Website</label>
                            <input type="url" name="website" value="{{ old('website') }}" placeholder="https://..."
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 mt-8 pt-8 border-t">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4 rounded-lg font-bold hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Save Travel Package
                </button>
                <a href="{{ route('travel-agent.packages.index') }}" 
                   class="flex-1 bg-gray-500 text-white px-6 py-4 rounded-lg font-bold hover:bg-gray-600 transition text-center">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function addDestinasiField() {
    const container = document.getElementById('destinasi-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'destinasi[]';
    input.placeholder = 'Input Destination...';
    input.className = 'w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition';
    container.appendChild(input);
}

function addIncludeField() {
    const container = document.getElementById('include-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'fasilitas_include[]';
    input.placeholder = 'Input Included Facilities...';
    input.className = 'w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition';
    container.appendChild(input);
}

function addExcludeField() {
    const container = document.getElementById('exclude-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'fasilitas_exclude[]';
    input.placeholder = 'Masukkan fasilitas...';
    input.className = 'w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 transition';
    container.appendChild(input);
}
</script>
@endsection