@extends('layouts.admin')

@section('title', 'Manage Tourism Owners')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex flex-col sm:flex-row 
            sm:items-center sm:justify-between 
            gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-user-tie text-primary"></i>
                Manage Tourism Owners
            </h1>
            <p class="text-gray-500 mt-2">Manage registered tourism owner accounts in the system</p>
        </div>
        <a href="{{ route('admin.pemilik.create') }}" 
           class="bg-gradient-to-r from-green-500 to-green-600 
       hover:from-green-600 hover:to-green-700 
       text-white 
       w-full sm:w-auto
       text-sm sm:text-base
       px-4 sm:px-6 
       py-2.5 sm:py-3 
       rounded-lg 
       transition 
       shadow-lg hover:shadow-xl 
       transform hover:-translate-y-1 
       flex items-center justify-center gap-2">
            <i class="fas fa-plus"></i>
            Add Tourism Owner
        </a>
    </div>
</div>

<!-- Success Alert -->
@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow">
    <div class="flex items-center">
        <i class="fas fa-check-circle text-2xl mr-3"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
</div>
@endif

<!-- Table Card -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    @if($pemilikWisata->count() > 0)
    <div class="overflow-x-auto">
    <table class="w-full text-xs sm:text-sm">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-primary to-blue-400 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold">No</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Name</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Email</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Phone</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Destinations</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Registered</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($pemilikWisata as $index => $pemilik)
                <tr class="hover:bg-accent/30 transition">
                    <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-primary/20 flex items-center justify-center mr-3">
                                <span class="text-primary font-bold text-sm">{{ strtoupper(substr($pemilik->name, 0, 2)) }}</span>
                            </div>
                            <span class="font-semibold text-gray-800">{{ $pemilik->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $pemilik->email }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $pemilik->no_telepon ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @if($pemilik->destinasi->isNotEmpty())
                            <div class="flex flex-wrap gap-1">
                                @foreach($pemilik->destinasi as $destinasi)
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">
                                        {{ $destinasi->nama_destinasi }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400">No destinations yet</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $pemilik->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium flex items-center gap-1 w-fit">
                            <i class="fas fa-check-circle"></i>
                            Active
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <a href="{{ route('admin.pemilik.edit', $pemilik->id) }}"  
                            class="bg-yellow-500 hover:bg-yellow-600 
                            text-white 
                            w-full sm:w-auto
                            px-3 py-1.5 
                            rounded-lg 
                            transition 
                            text-xs 
                            flex items-center justify-center gap-1"
                                <i class="fas fa-edit"></i>
                                Edit
                            </a>
                            <form method="POST" 
                                  action="{{ route('admin.pemilik.destroy', $pemilik->id) }}"
                                  onsubmit="return confirm('Are you sure you want to delete this tourism owner?')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 hover:bg-red-600 
                                        text-white 
                                        w-full sm:w-auto
                                        px-3 py-1.5 
                                        rounded-lg 
                                        transition 
                                        text-xs 
                                        flex items-center justify-center gap-1"
                                    <i class="fas fa-trash"></i>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <!-- Empty State -->
    <div class="py-20 px-6 text-center">
        <div class="flex flex-col items-center justify-center text-gray-400">
            <i class="fas fa-users-slash text-7xl mb-5"></i>
            <h3 class="text-2xl font-bold text-gray-600 mb-2">No Tourism Owners Yet</h3>
            <p class="text-gray-500 mb-6">Click "Add Tourism Owner" to create a new account</p>
            <a href="{{ route('admin.pemilik.create') }}" 
               class="bg-gradient-to-r from-primary to-blue-400 hover:from-blue-400 hover:to-primary text-white px-6 py-3 rounded-lg transition shadow-lg hover:shadow-xl flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Add Tourism Owner
            </a>
        </div>
    </div>
    @endif
</div>
@endsection