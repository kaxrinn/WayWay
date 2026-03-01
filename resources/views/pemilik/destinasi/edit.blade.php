@extends('layouts.pemilik')

@section('title', 'Edit Destination')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-0">

    <!-- Back Button -->
    <a href="{{ route('pemilik.destinasi.index') }}" 
       class="inline-flex items-center text-gray-600 hover:text-primary transition mb-6">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to List
    </a>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-4 sm:px-8 py-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-edit"></i>
                Edit Tourist Destination
            </h1>
            <p class="text-white/90 mt-2">Edit: <strong>{{ $destinasi->nama_destinasi }}</strong></p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('pemilik.destinasi.update', $destinasi->id) }}" enctype="multipart/form-data" class="p-4 sm:p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Destination Name -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Destination Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="nama_destinasi" 
                           value="{{ old('nama_destinasi', $destinasi->nama_destinasi) }}"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg"
                           required>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Category *</label>
                    <select name="kategori_id" class="w-full px-4 py-3 border-2 rounded-lg" required>
                        <option value="">-- Select Category --</option>
                        @foreach($kategori as $kategori)
                            <option value="{{ $kategori->id }}" 
                                {{ old('kategori_id', $destinasi->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Price *</label>
                    <input type="number" name="harga" value="{{ old('harga', $destinasi->harga) }}"
                           class="w-full px-4 py-3 border-2 rounded-lg" required>
                </div>

                <!-- Latitude -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Latitude *</label>
                    <input type="text" name="latitude" value="{{ old('latitude', $destinasi->latitude) }}"
                           class="w-full px-4 py-3 border-2 rounded-lg" required>
                </div>

                <!-- Longitude -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Longitude *</label>
                    <input type="text" name="longitude" value="{{ old('longitude', $destinasi->longitude) }}"
                           class="w-full px-4 py-3 border-2 rounded-lg" required>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">Description *</label>
                    <textarea name="deskripsi" rows="4"
                        class="w-full px-4 py-3 border-2 rounded-lg" required>{{ old('deskripsi', $destinasi->deskripsi) }}</textarea>
                </div>

                <!-- Current Photos -->
                @if($destinasi->foto && count($destinasi->foto) > 0)
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-2">Current Photos</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($destinasi->foto as $index => $foto)
                        <div class="relative group">
                            <img src="{{ Storage::url($foto) }}" class="w-full h-32 object-cover rounded-lg shadow">
                            <label class="absolute top-2 right-2 cursor-pointer">
                                <input type="checkbox" name="delete_foto[]" value="{{ $foto }}" class="w-5 h-5">
                                <span class="absolute inset-0 bg-red-500 text-white flex items-center justify-center rounded opacity-0 group-hover:opacity-90 transition text-xs font-bold">
                                    Delete
                                </span>
                            </label>
                            <div class="absolute bottom-2 left-2 bg-black/50 text-white px-2 py-1 rounded text-xs">
                                Photo {{ $index + 1 }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Check photos you want to delete</p>
                </div>
                @endif

                <!-- Upload New Photos -->
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-2">
                        Add New Photos (Remaining slots: {{ max(0, ($limits['max_foto'] ?? 999) - count($destinasi->foto ?? [])) }})
                    </label>
                    <input type="file" name="foto[]" multiple accept="image/*"
                           class="w-full px-4 py-3 border-2 border-dashed rounded-lg">
                    <div id="fotoPreview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-4"></div>
                </div>

                <!-- Current Videos -->
                @if($destinasi->video && count($destinasi->video) > 0)
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-2">Current Videos</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($destinasi->video as $index => $video)
                        <div class="relative group bg-gray-100 rounded-lg p-4">
                            <video src="{{ Storage::url($video) }}" class="w-full h-32 rounded" controls></video>
                            <label class="absolute top-2 right-2 cursor-pointer">
                                <input type="checkbox" name="delete_video[]" value="{{ $video }}" class="w-5 h-5">
                            </label>
                            <p class="text-xs text-gray-600 mt-2">Video {{ $index + 1 }}</p>
                        </div>
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Check videos you want to delete</p>
                </div>
                @endif

                <!-- Upload New Videos -->
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-2">
                        Add New Videos (Remaining slots: {{ max(0, ($limits['max_video'] ?? 0) - count($destinasi->video ?? [])) }})
                    </label>
                    <input type="file" name="video[]" multiple accept="video/*"
                           class="w-full px-4 py-3 border-2 border-dashed rounded-lg">
                    <div id="videoPreview" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4"></div>
                </div>

                <!-- Featured -->
                @if($limits['is_featured_allowed'])
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer p-4 bg-yellow-50 border-2 border-yellow-200 rounded-lg">
                        <input type="checkbox" name="is_featured" value="1" {{ $destinasi->is_featured ? 'checked' : '' }}>
                        <div>
                            <p class="font-semibold text-gray-800">Set as Featured Destination</p>
                            <p class="text-sm text-gray-600">Destination will appear highlighted on homepage</p>
                        </div>
                    </label>
                </div>
                @endif
            </div>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 mt-8">
                <button type="submit" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-4 rounded-lg font-semibold">
                    <i class="fas fa-save"></i> Update Destination
                </button>
                <a href="{{ route('pemilik.destinasi.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-4 rounded-lg text-center font-semibold">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>

        </form>
    </div>
</div>
@endsection