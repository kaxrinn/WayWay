@extends('layouts.pemilik')

@section('title', 'Edit Promotion Banner')

@section('content')
<div class="max-w-3xl mx-auto">

    <!-- Back -->
    <a href="{{ route('pemilik.promosi.index') }}"
       class="inline-flex items-center text-gray-600 hover:text-yellow-600 transition mb-6 text-sm sm:text-base">
        <i class="fas fa-arrow-left mr-2"></i> Back
    </a>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-5 sm:px-8 py-5 sm:py-6">
            <h1 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-edit"></i> Edit Promotion Banner
            </h1>
            <p class="text-white/80 text-sm mt-1">Update your currently active promotion banner</p>
        </div>

        <form method="POST"
              action="{{ route('pemilik.promosi.update', $promosi->id) }}"
              enctype="multipart/form-data"
              class="p-5 sm:p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <!-- Banner Title -->
                <div>
                    <label for="judul_banner" class="block text-gray-700 font-semibold mb-2 text-sm sm:text-base">
                        Banner Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="judul_banner"
                           name="judul_banner"
                           value="{{ old('judul_banner', $promosi->judul_banner) }}"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-4 focus:ring-yellow-500/20 transition text-sm sm:text-base @error('judul_banner') border-red-500 @enderror"
                           required>
                    @error('judul_banner')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="deskripsi_banner" class="block text-gray-700 font-semibold mb-2 text-sm sm:text-base">
                        Description <span class="text-gray-400 font-normal text-sm">(optional)</span>
                    </label>
                    <textarea id="deskripsi_banner"
                              name="deskripsi_banner"
                              rows="3"
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-4 focus:ring-yellow-500/20 transition text-sm sm:text-base">{{ old('deskripsi_banner', $promosi->deskripsi_banner) }}</textarea>
                    @error('deskripsi_banner')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Banner -->
                <div>
                    <p class="block text-gray-700 font-semibold mb-2 text-sm sm:text-base">Current Banner</p>
                    <div class="relative rounded-xl overflow-hidden border border-gray-200 shadow">
                        <img src="{{ Storage::url($promosi->banner_promosi) }}"
                             alt="{{ $promosi->judul_banner }}"
                             class="w-full max-h-56 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent flex items-end p-3">
                            <span class="text-white text-xs font-semibold bg-black/50 px-2 py-1 rounded">Current image</span>
                        </div>
                    </div>
                </div>

                <!-- Upload New Image -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2 text-sm sm:text-base">
                        Replace Banner Image
                        <span class="text-gray-400 font-normal text-sm">(leave empty to keep current)</span>
                    </label>
                    <div class="border-2 border-dashed border-yellow-300 rounded-xl p-5 sm:p-6 hover:border-yellow-500 transition cursor-pointer"
                         onclick="document.getElementById('banner_promosi').click()">
                        <div class="text-center" id="uploadPlaceholder">
                            <i class="fas fa-cloud-upload-alt text-3xl sm:text-4xl text-yellow-300 mb-2"></i>
                            <p class="text-gray-500 text-sm">Click to select a new image</p>
                            <p class="text-xs text-gray-400">JPG, PNG, WEBP · Max 2MB · Recommended 1200×628px</p>
                        </div>
                        <div class="hidden text-center" id="uploadPreview">
                            <img id="previewImg" src="" alt="Preview" class="max-h-40 mx-auto rounded-lg shadow mb-2">
                            <p class="text-green-600 text-sm font-medium" id="previewName"></p>
                        </div>
                    </div>
                    <input type="file"
                           id="banner_promosi"
                           name="banner_promosi"
                           accept="image/jpg,image/jpeg,image/png,image/webp"
                           class="hidden"
                           onchange="previewBanner(this)">
                    @error('banner_promosi')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mt-8">
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white px-6 py-3 sm:py-4 rounded-lg transition shadow-lg font-semibold text-base sm:text-lg flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('pemilik.promosi.index') }}"
                   class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 sm:py-4 rounded-lg transition shadow-lg font-semibold text-base sm:text-lg flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
function previewBanner(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('uploadPlaceholder').classList.add('hidden');
            document.getElementById('uploadPreview').classList.remove('hidden');
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('previewName').textContent = input.files[0].name;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush