@extends('layouts.pemilik')

@section('title', 'Premium Promotion Banner')

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Header -->
    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl px-5 sm:px-8 py-5 sm:py-6 mb-6 shadow-lg">
        <div class="flex items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                    <i class="fas fa-bullhorn"></i>
                    Premium Promotion Banner
                </h1>
                <p class="text-white/90 mt-1 text-sm">Upload a promotion banner that will appear on the tourist homepage</p>
            </div>
            <div class="bg-white/20 px-3 sm:px-4 py-2 rounded-lg text-white text-center flex-shrink-0">
                <p class="text-xs text-white/80">Active Package</p>
                <p class="font-bold text-sm sm:text-base">{{ $paket->nama_paket }}</p>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow flex items-center gap-3">
        <i class="fas fa-check-circle text-xl sm:text-2xl flex-shrink-0"></i>
        <span class="font-medium text-sm sm:text-base">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 shadow flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-xl sm:text-2xl flex-shrink-0"></i>
        <span class="font-medium text-sm sm:text-base">{{ session('error') }}</span>
    </div>
    @endif

    @if($promosi)
    <!-- Banner Exists -->
    <div class="bg-white rounded-xl shadow-xl overflow-hidden mb-6">
        <div class="bg-gray-50 px-4 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-3">
            <h2 class="text-base sm:text-lg font-semibold text-gray-700 flex items-center gap-2">
                <i class="fas fa-image text-yellow-500"></i>
                Current Promotion Banner
            </h2>
            <!-- Status Badge -->
            @if($promosi->status === 'active')
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap">
                    <i class="fas fa-circle text-green-500 mr-1" style="font-size:8px"></i> Active
                </span>
            @elseif($promosi->status === 'pending')
                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap">
                    <i class="fas fa-clock mr-1"></i> Pending
                </span>
            @else
                <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap">
                    <i class="fas fa-times-circle mr-1"></i> Expired
                </span>
            @endif
        </div>

        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <!-- Banner Preview -->
                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-2 uppercase tracking-wide">Banner Preview</p>
                    <div class="relative rounded-xl overflow-hidden shadow-lg border border-gray-200">
                        <img src="{{ Storage::url($promosi->banner_promosi) }}"
                             alt="{{ $promosi->judul_banner }}"
                             class="w-full object-cover max-h-64">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end p-4">
                            <div>
                                <p class="text-white font-bold text-base sm:text-lg leading-tight">{{ $promosi->judul_banner }}</p>
                                @if($promosi->deskripsi_banner)
                                    <p class="text-white/80 text-xs mt-1">{{ Str::limit($promosi->deskripsi_banner, 80) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Info -->
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Banner Title</p>
                        <p class="text-gray-800 font-semibold text-base sm:text-lg">{{ $promosi->judul_banner }}</p>
                    </div>

                    @if($promosi->deskripsi_banner)
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Description</p>
                        <p class="text-gray-700 text-sm leading-relaxed">{{ $promosi->deskripsi_banner }}</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <p class="text-xs text-blue-500 font-semibold mb-1">Start Date</p>
                            <p class="text-blue-800 font-bold text-sm">{{ $promosi->tanggal_mulai->format('d M Y') }}</p>
                        </div>
                        <div class="bg-red-50 p-3 rounded-lg">
                            <p class="text-xs text-red-500 font-semibold mb-1">End Date</p>
                            <p class="text-red-800 font-bold text-sm">{{ $promosi->tanggal_selesai->format('d M Y') }}</p>
                        </div>
                    </div>

                    @php
                        $today = now()->startOfDay();
                        $end = \Carbon\Carbon::parse($promosi->tanggal_selesai)->startOfDay();
                        $sisaHari = $today->diffInDays($end, false);
                    @endphp
                    @if($sisaHari >= 0)
                    <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg flex items-center gap-2">
                        <i class="fas fa-hourglass-half text-yellow-500 flex-shrink-0"></i>
                        <p class="text-sm text-yellow-800 font-medium">
                            <strong>{{ $sisaHari }} days</strong> remaining
                        </p>
                    </div>
                    @else
                    <div class="bg-red-50 border border-red-200 p-3 rounded-lg flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-red-500 flex-shrink-0"></i>
                        <p class="text-sm text-red-800 font-medium">Banner has expired</p>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('pemilik.promosi.edit', $promosi->id) }}"
                           class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-3 rounded-lg transition font-semibold text-sm text-center flex items-center justify-center gap-2">
                            <i class="fas fa-edit"></i> Edit Banner
                        </a>
                        <form method="POST"
                              action="{{ route('pemilik.promosi.destroy', $promosi->id) }}"
                              onsubmit="return confirm('Are you sure you want to delete this promotion banner?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-lg transition font-semibold text-sm flex items-center gap-2">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else
    <!-- No Banner Yet - Upload Form -->
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-gray-50 px-4 sm:px-6 py-4 border-b border-gray-200">
            <h2 class="text-base sm:text-lg font-semibold text-gray-700 flex items-center gap-2">
                <i class="fas fa-upload text-yellow-500"></i>
                Upload New Promotion Banner
            </h2>
        </div>

        <form method="POST" action="{{ route('pemilik.promosi.store') }}" enctype="multipart/form-data" class="p-5 sm:p-8">
            @csrf

            <div class="grid grid-cols-1 gap-6">
                <!-- Banner Title -->
                <div>
                    <label for="judul_banner" class="block text-gray-700 font-semibold mb-2 text-sm sm:text-base">
                        Banner Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="judul_banner"
                           name="judul_banner"
                           value="{{ old('judul_banner') }}"
                           placeholder="e.g. Year-End Sale - 50% Off!"
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
                              placeholder="Additional information to display in the banner popup..."
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-4 focus:ring-yellow-500/20 transition text-sm sm:text-base @error('deskripsi_banner') border-red-500 @enderror">{{ old('deskripsi_banner') }}</textarea>
                    @error('deskripsi_banner')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Maximum 500 characters. Shown when a tourist clicks the banner.</p>
                </div>

                <!-- Upload Image -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2 text-sm sm:text-base">
                        Banner Image <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-yellow-300 rounded-xl p-6 sm:p-8 hover:border-yellow-500 transition cursor-pointer"
                         onclick="document.getElementById('banner_promosi').click()">
                        <div class="text-center" id="uploadPlaceholder">
                            <i class="fas fa-image text-5xl sm:text-6xl text-yellow-300 mb-4"></i>
                            <p class="text-gray-600 font-medium mb-1 text-sm sm:text-base">Click to upload a banner image</p>
                            <p class="text-xs text-gray-400">Format: JPG, PNG, WEBP · Max 2MB</p>
                            <p class="text-xs text-gray-400 mt-1">Recommended size: <strong>1200 × 628 px</strong> (16:9)</p>
                        </div>
                        <div class="hidden text-center" id="uploadPreview">
                            <img id="previewImg" src="" alt="Preview" class="max-h-48 mx-auto rounded-lg shadow-lg mb-3">
                            <p class="text-green-600 font-medium text-sm" id="previewName"></p>
                        </div>
                    </div>
                    <input type="file"
                           id="banner_promosi"
                           name="banner_promosi"
                           accept="image/jpg,image/jpeg,image/png,image/webp"
                           class="hidden"
                           onchange="previewBanner(this)"
                           required>
                    @error('banner_promosi')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex gap-3">
                <i class="fas fa-info-circle text-yellow-500 text-lg sm:text-xl flex-shrink-0 mt-0.5"></i>
                <div class="text-sm text-yellow-800">
                    <p class="font-semibold mb-1">Banner Display Information</p>
                    <ul class="space-y-1 text-yellow-700 text-sm">
                        <li>• Banner will be active immediately after upload</li>
                        <li>• Displayed for the duration of your active Premium Package</li>
                        <li>• Only 1 banner can be active at a time</li>
                        <li>• Banner will appear in a slideshow on the tourist homepage</li>
                    </ul>
                </div>
            </div>

            <!-- Submit -->
            <div class="mt-8">
                <button type="submit"
                        class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white px-6 py-3 sm:py-4 rounded-lg transition shadow-lg hover:shadow-xl font-semibold text-base sm:text-lg flex items-center justify-center gap-2">
                    <i class="fas fa-upload"></i>
                    Upload Promotion Banner
                </button>
            </div>
        </form>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function previewBanner(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            document.getElementById('uploadPlaceholder').classList.add('hidden');
            document.getElementById('uploadPreview').classList.remove('hidden');
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('previewName').textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
        };

        reader.readAsDataURL(file);
    }
}
</script>
@endpush