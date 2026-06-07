@extends('layouts.admin')

@section('title', 'Edit Paket')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.travel-subscriptions.packages.index') }}" class="text-blue-600 hover:text-blue-800 mb-6 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>

    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 px-8 py-6">
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-edit"></i>
                Edit Paket Subscription
            </h1>
            <p class="text-purple-100 mt-2">Update {{ $package->nama_paket }}</p>
        </div>
        
        <!-- Form -->
        <form method="POST" action="{{ route('admin.travel-subscriptions.packages.update', $package->id) }}" class="p-8">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Nama Paket -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Name Package *</label>
                    <input type="text" name="nama_paket" value="{{ old('nama_paket', $package->nama_paket) }}" required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('nama_paket') border-red-500 @enderror">
                    @error('nama_paket')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Description Packages</label>
                    <textarea name="deskripsi" rows="3"
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $package->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <!-- Harga -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Price (Rp) *</label>
                        <input type="number" name="harga" value="{{ old('harga', $package->harga) }}" min="0" step="1000" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('harga') border-red-500 @enderror">
                        @error('harga')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max Packages -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Max Package *</label>
                        <input type="number" name="max_packages" value="{{ old('max_packages', $package->max_packages) }}" min="1" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('max_packages') border-red-500 @enderror">
                        @error('max_packages')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Durasi -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Duration (Months) *</label>
                        <input type="number" name="durasi_bulan" value="{{ old('durasi_bulan', $package->durasi_bulan) }}" min="0" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('durasi_bulan') border-red-500 @enderror">
                        @error('durasi_bulan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Status *</label>
                        <select name="status" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('status') border-red-500 @enderror">
                            <option value="active" {{ old('status', $package->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $package->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Fitur (Checkboxes) -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-4">Package Features</label>
                    <div class="space-y-3 bg-gray-50 p-4 rounded-lg">
                        @php
                            $allFeatures = ['dashboard', 'basic_listing', 'featured_listing', 'featured_badge', 'homepage_recommendation', 'priority_ai_recommendation', 'analytics', 'better_visibility'];
                            $selectedFeatures = old('fitur', $package->fitur ?? []);
                        @endphp

                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="dashboard" class="mr-3" {{ in_array('dashboard', $selectedFeatures) ? 'checked' : '' }}>
                            <span class="text-gray-700">📊 Dashboard</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="basic_listing" class="mr-3" {{ in_array('basic_listing', $selectedFeatures) ? 'checked' : '' }}>
                            <span class="text-gray-700">📝 Basic Listing</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="featured_listing" class="mr-3" {{ in_array('featured_listing', $selectedFeatures) ? 'checked' : '' }}>
                            <span class="text-gray-700">⭐ Featured Listing</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="featured_badge" class="mr-3" {{ in_array('featured_badge', $selectedFeatures) ? 'checked' : '' }}>
                            <span class="text-gray-700">🏆 Featured Badge</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="homepage_recommendation" class="mr-3" {{ in_array('homepage_recommendation', $selectedFeatures) ? 'checked' : '' }}>
                            <span class="text-gray-700">🏠 Homepage Recommendation</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="priority_ai_recommendation" class="mr-3" {{ in_array('priority_ai_recommendation', $selectedFeatures) ? 'checked' : '' }}>
                            <span class="text-gray-700">🤖 Priority AI Recommendation</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="analytics" class="mr-3" {{ in_array('analytics', $selectedFeatures) ? 'checked' : '' }}>
                            <span class="text-gray-700">📈 Analytics Dashboard</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="better_visibility" class="mr-3" {{ in_array('better_visibility', $selectedFeatures) ? 'checked' : '' }}>
                            <span class="text-gray-700">👁️ Better Visibility</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-4 rounded-lg font-bold hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
                <a href="{{ route('admin.travel-subscriptions.packages.index') }}" 
                   class="flex-1 bg-gray-500 text-white px-6 py-4 rounded-lg font-bold hover:bg-gray-600 transition text-center">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection