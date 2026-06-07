@extends('layouts.admin')

@section('title', 'Edit Travel Agent')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-8 py-6">
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-edit"></i>
                Edit Travel Agent
            </h1>
            <p class="text-yellow-100 mt-2">Update data {{ $travelAgent->name }}</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.travel-agents.update', $travelAgent->id) }}" class="p-8">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Nama -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $travelAgent->name) }}" required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $travelAgent->email) }}" required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No Telepon -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Phone Number *</label>
                    <input type="text" name="no_telepon" value="{{ old('no_telepon', $travelAgent->no_telepon) }}" required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition @error('no_telepon') border-red-500 @enderror">
                    @error('no_telepon')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">New Password</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirm -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Confirm New Password</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition">
                </div>

                <!-- Info -->
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                    <p class="text-yellow-800 text-sm">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Created:</strong> {{ $travelAgent->created_at->format('d M Y H:i') }}
                    </p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-6 py-3 rounded-lg font-bold hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
                <a href="{{ route('admin.travel-agents.index') }}" class="flex-1 bg-gray-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-gray-600 transition text-center">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection