@extends('layouts.travel-agent')

@section('title', 'Edit Tour Package')

@section('content')
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-8 py-6">
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-edit"></i>
                Edit Tour Package
            </h1>
            <p class="text-yellow-100 mt-2">Update {{ $package->nama_paket }}</p>
        </div>
        
        <!-- Form -->
        <form method="POST" action="{{ route('travel-agent.packages.update', $package->id) }}" enctype="multipart/form-data" class="p-8">
            @csrf
            @method('PUT')
            
            <div class="space-y-8">
                <!-- BASIC INFORMATION -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-yellow-500">
                        <i class="fas fa-info-circle text-yellow-500 mr-2"></i>
                        Basic Information
                    </h3>

                    <div class="space-y-4">
                        <!-- Package Name -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Tour Package Name *</label>
                            <input type="text" name="nama_paket" value="{{ old('nama_paket', $package->nama_paket) }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition @error('nama_paket') border-red-500 @enderror">
                            @error('nama_paket')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Package Description *</label>
                            <textarea name="deskripsi" rows="4" required
                                      class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $package->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Thumbnail -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Banner / Main Photo</label>
                            @if($package->thumbnail)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $package->thumbnail) }}" alt="Thumbnail" class="h-32 rounded-lg object-cover">
                                <p class="text-gray-600 text-xs mt-2">Current photo</p>
                            </div>
                            @endif
                            <input type="file" name="thumbnail" accept="image/*"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 @error('thumbnail') border-red-500 @enderror">
                            <p class="text-gray-500 text-xs mt-1">Leave empty to keep the current photo</p>
                            @error('thumbnail')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <!-- Price -->
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Price per Person (Rp) *</label>
                                <input type="number" name="harga_per_orang" value="{{ old('harga_per_orang', $package->harga_per_orang) }}" min="0" step="1000" required
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition @error('harga_per_orang') border-red-500 @enderror">
                                @error('harga_per_orang')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Duration -->
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Duration (Days) *</label>
                                <input type="number" name="durasi_hari" value="{{ old('durasi_hari', $package->durasi_hari) }}" min="1" required
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition @error('durasi_hari') border-red-500 @enderror">
                                @error('durasi_hari')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Departure Date -->
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Departure Date *</label>
                                <input type="date" name="tanggal_keberangkatan" value="{{ old('tanggal_keberangkatan', $package->tanggal_keberangkatan->format('Y-m-d')) }}" required
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition @error('tanggal_keberangkatan') border-red-500 @enderror">
                                @error('tanggal_keberangkatan')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DESTINATIONS -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-green-500">
                        <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>
                        Destinations to Visit
                    </h3>

                    <div id="destinasi-container" class="space-y-2">
                        @foreach($package->destinasi ?? [] as $key => $destinasi)
                        <div class="flex gap-2">
                            <input type="text" name="destinasi[]" value="{{ $destinasi }}"
                                   class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition">
                            <button type="button" onclick="removeDestinasiField(this)" class="px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        @endforeach
                        @if(!($package->destinasi && count($package->destinasi) > 0))
                        <input type="text" name="destinasi[]" placeholder="e.g. Barelang Bridge"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition">
                        @endif
                    </div>
                    <button type="button" onclick="addDestinasiField()" class="mt-3 text-green-600 hover:text-green-800 font-semibold text-sm">
                        <i class="fas fa-plus mr-1"></i> Add Destination
                    </button>
                </div>

                <!-- INCLUDED FACILITIES -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-blue-500">
                        <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                        Included Facilities
                    </h3>

                    <div id="include-container" class="space-y-2">
                        @foreach($package->fasilitas_include ?? [] as $key => $fasilitas)
                        <div class="flex gap-2">
                            <input type="text" name="fasilitas_include[]" value="{{ $fasilitas }}"
                                   class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                            <button type="button" onclick="removeIncludeField(this)" class="px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        @endforeach
                        @if(!($package->fasilitas_include && count($package->fasilitas_include) > 0))
                        <input type="text" name="fasilitas_include[]" placeholder="e.g. Transportation"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                        @endif
                    </div>
                    <button type="button" onclick="addIncludeField()" class="mt-3 text-blue-600 hover:text-blue-800 font-semibold text-sm">
                        <i class="fas fa-plus mr-1"></i> Add Facility
                    </button>
                </div>

                <!-- EXCLUDED FACILITIES -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-red-500">
                        <i class="fas fa-times-circle text-red-500 mr-2"></i>
                        Excluded Facilities
                    </h3>

                    <div id="exclude-container" class="space-y-2">
                        @foreach($package->fasilitas_exclude ?? [] as $key => $fasilitas)
                        <div class="flex gap-2">
                            <input type="text" name="fasilitas_exclude[]" value="{{ $fasilitas }}"
                                   class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 transition">
                            <button type="button" onclick="removeExcludeField(this)" class="px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        @endforeach
                        @if(!($package->fasilitas_exclude && count($package->fasilitas_exclude) > 0))
                        <input type="text" name="fasilitas_exclude[]" placeholder="e.g. Accommodation"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 transition">
                        @endif
                    </div>
                    <button type="button" onclick="addExcludeField()" class="mt-3 text-red-600 hover:text-red-800 font-semibold text-sm">
                        <i class="fas fa-plus mr-1"></i> Add Facility
                    </button>
                </div>

                <!-- PARTICIPANTS -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-purple-500">
                        <i class="fas fa-users text-purple-500 mr-2"></i>
                        Participant Information
                    </h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Minimum Participants *</label>
                            <input type="number" name="min_peserta" value="{{ old('min_peserta', $package->min_peserta) }}" min="1" required
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('min_peserta') border-red-500 @enderror">
                            @error('min_peserta')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Maximum Participants *</label>
                            <input type="number" name="max_peserta" value="{{ old('max_peserta', $package->max_peserta) }}" min="1" required
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('max_peserta') border-red-500 @enderror">
                            @error('max_peserta')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-gray-700 font-semibold mb-2">Meeting Point *</label>
                        <input type="text" name="meeting_point" value="{{ old('meeting_point', $package->meeting_point) }}" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('meeting_point') border-red-500 @enderror">
                        @error('meeting_point')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- CONTACT -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-orange-500">
                        <i class="fas fa-phone text-orange-500 mr-2"></i>
                        Your Contact
                    </h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">WhatsApp</label>
                            <input type="text" name="whatsapp" value="{{ old('whatsapp', $package->whatsapp) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $package->email) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Instagram</label>
                            <input type="text" name="instagram" value="{{ old('instagram', $package->instagram) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Website</label>
                            <input type="url" name="website" value="{{ old('website', $package->website) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 mt-8 pt-8 border-t">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-6 py-4 rounded-lg font-bold hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Save Changes
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
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" name="destinasi[]" placeholder="Enter destination..." 
               class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition">
        <button type="button" onclick="removeDestinasiField(this)" class="px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

function removeDestinasiField(btn) {
    btn.parentElement.remove();
}

function addIncludeField() {
    const container = document.getElementById('include-container');
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" name="fasilitas_include[]" placeholder="Enter facility..." 
               class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
        <button type="button" onclick="removeIncludeField(this)" class="px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

function removeIncludeField(btn) {
    btn.parentElement.remove();
}

function addExcludeField() {
    const container = document.getElementById('exclude-container');
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" name="fasilitas_exclude[]" placeholder="Enter facility..." 
               class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 transition">
        <button type="button" onclick="removeExcludeField(this)" class="px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

function removeExcludeField(btn) {
    btn.parentElement.remove();
}
</script>
@endsection