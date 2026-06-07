@extends('layouts.admin')

@section('title', 'Add Travel Agent')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-8 py-6">
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-plus"></i>
                Add Travel Agent
            </h1>
            <p class="text-yellow-100 mt-2">Create a new travel agent account.</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.travel-agents.store') }}" class="p-8">
            @csrf

            <div class="space-y-6">
                <!-- Nama -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No Telepon -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Phone Number *</label>
                    <input type="text" name="no_telepon" value="{{ old('no_telepon') }}" required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition @error('no_telepon') border-red-500 @enderror">
                    @error('no_telepon')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Password *</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirm -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Confirm Password *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-6 py-3 rounded-lg font-bold hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Save
                </button>
                <a href="{{ route('admin.travel-agents.index') }}" class="flex-1 bg-gray-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-gray-600 transition text-center">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection