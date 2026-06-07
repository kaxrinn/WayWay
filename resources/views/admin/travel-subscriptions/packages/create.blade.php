@extends('layouts.admin')

@section('title', 'Buat Paket Baru')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 px-8 py-6">
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-plus-circle"></i>
                Make a new Subcriptions
            </h1>
            <p class="text-purple-100 mt-2">Add New Package to sell for Travel Agents</p>
        </div>
        
        <!-- Form -->
        <form method="POST" action="{{ route('admin.travel-subscriptions.packages.store') }}" class="p-8">
            @csrf
            
            <div class="space-y-6">
                <!-- Nama Paket -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Package Name *</label>
                    <input type="text" name="nama_paket" value="{{ old('nama_paket') }}" placeholder="Example: Basic, Silver, Gold" required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('nama_paket') border-red-500 @enderror">
                    @error('nama_paket')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Package Description</label>
                    <textarea name="deskripsi" rows="3" placeholder="Explain the benefits of this package..."
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <!-- Harga -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Price (Rp) *</label>
                        <input type="number" name="harga" value="{{ old('harga', 0) }}" min="0" step="1000" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('harga') border-red-500 @enderror">
                        <p class="text-gray-500 text-xs mt-1">Enter 0 for free package</p>
                        @error('harga')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max Packages -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Max Package *</label>
                        <input type="number" name="max_packages" value="{{ old('max_packages', 1) }}" min="1" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('max_packages') border-red-500 @enderror">
                        <p class="text-gray-500 text-xs mt-1">Maximum package can create</p>
                        @error('max_packages')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Durasi -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Duration (Months) *</label>
                        <input type="number" name="durasi_bulan" value="{{ old('durasi_bulan', 1) }}" min="0" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('durasi_bulan') border-red-500 @enderror">
                        <p class="text-gray-500 text-xs mt-1">0 = Lifetime</p>
                        @error('durasi_bulan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Status *</label>
                        <select name="status" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition @error('status') border-red-500 @enderror">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Nonactive</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Fitur (Checkboxes) -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-4">Fitur Paket</label>
                    <div class="space-y-3 bg-gray-50 p-4 rounded-lg">
                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="dashboard" class="mr-3">
                            <span class="text-gray-700">📊 Dashboard</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="basic_listing" class="mr-3">
                            <span class="text-gray-700">📝 Basic Listing</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="featured_listing" class="mr-3">
                            <span class="text-gray-700">⭐ Featured Listing</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="featured_badge" class="mr-3">
                            <span class="text-gray-700">🏆 Featured Badge</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="homepage_recommendation" class="mr-3">
                            <span class="text-gray-700">🏠 Homepage Recommendation</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="priority_ai_recommendation" class="mr-3">
                            <span class="text-gray-700">🤖 Priority AI Recommendation</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="analytics" class="mr-3">
                            <span class="text-gray-700">📈 Analytics Dashboard</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fitur[]" value="better_visibility" class="mr-3">
                            <span class="text-gray-700">👁️ Better Visibility</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-4 rounded-lg font-bold hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Save Packages
                </button>
                <a href="{{ route('admin.travel-subscriptions.packages.index') }}" 
                   class="flex-1 bg-gray-500 text-white px-6 py-4 rounded-lg font-bold hover:bg-gray-600 transition text-center">
                    <i class="fas fa-times mr-2"></i> Cancel`
                </a>
            </div>
        </form>
    </div>
</div>
@endsection