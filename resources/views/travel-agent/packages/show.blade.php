@extends('layouts.travel-agent')

@section('title', $package->nama_paket)

@section('content')
<div class="max-w-5xl mx-auto">
    <a href="{{ route('travel-agent.packages.index') }}" class="text-blue-600 hover:text-blue-800 mb-6 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Package List
    </a>

    <!-- Header with Thumbnail -->
    <div class="bg-white rounded-xl shadow-xl overflow-hidden mb-6">
        @if($package->thumbnail)
        <img src="{{ asset('storage/' . $package->thumbnail) }}" alt="{{ $package->nama_paket }}" 
             class="w-full h-80 object-cover">
        @else
        <div class="w-full h-80 bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center">
            <i class="fas fa-image text-white text-6xl opacity-50"></i>
        </div>
        @endif

        <div class="p-8 bg-gradient-to-r from-blue-50 to-blue-100">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800">{{ $package->nama_paket }}</h1>
                    <p class="text-gray-600 mt-2">{{ $package->deskripsi }}</p>
                </div>
                <div class="text-right">
                    <p class="text-4xl font-bold text-green-600">Rp {{ number_format($package->harga_per_orang, 0, ',', '.') }}</p>
                    <p class="text-gray-600">per person</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-blue-500">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Basic Information
                </h2>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold uppercase">Trip Duration</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $package->durasi_hari }} Days</p>
                    </div>

                    <div>
                        <p class="text-gray-600 text-sm font-semibold uppercase">Departure Date</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $package->tanggal_keberangkatan->format('d M Y') }}</p>
                    </div>

                    <div>
                        <p class="text-gray-600 text-sm font-semibold uppercase">Minimum Participants</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $package->min_peserta }} People</p>
                    </div>

                    <div>
                        <p class="text-gray-600 text-sm font-semibold uppercase">Maximum Participants</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $package->max_peserta }} People</p>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t">
                    <p class="text-gray-600 text-sm font-semibold uppercase">Meeting Point</p>
                    <p class="text-lg text-gray-800">
                        <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                        {{ $package->meeting_point }}
                    </p>
                </div>
            </div>

            <!-- Destinations -->
            @if($package->destinasi && count($package->destinasi) > 0)
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-green-500">
                    <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>
                    Destinations to Visit
                </h2>

                <div class="space-y-3">
                    @foreach($package->destinasi as $idx => $destinasi)
                    <div class="flex items-start gap-3 pb-3 {{ !$loop->last ? 'border-b' : '' }}">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-100">
                                <span class="text-green-600 font-bold">{{ $idx + 1 }}</span>
                            </div>
                        </div>
                        <p class="text-lg text-gray-800">{{ $destinasi }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Included Facilities -->
            @if($package->fasilitas_include && count($package->fasilitas_include) > 0)
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-blue-500">
                    <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                    Included Facilities
                </h2>

                <div class="grid grid-cols-2 gap-4">
                    @foreach($package->fasilitas_include as $fasilitas)
                    <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                        <i class="fas fa-check text-blue-600 text-lg"></i>
                        <span class="text-gray-800">{{ $fasilitas }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Excluded Facilities -->
            @if($package->fasilitas_exclude && count($package->fasilitas_exclude) > 0)
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-4 border-b-2 border-red-500">
                    <i class="fas fa-times-circle text-red-500 mr-2"></i>
                    Excluded Facilities
                </h2>

                <div class="grid grid-cols-2 gap-4">
                    @foreach($package->fasilitas_exclude as $fasilitas)
                    <div class="flex items-center gap-3 p-3 bg-red-50 rounded-lg">
                        <i class="fas fa-times text-red-600 text-lg"></i>
                        <span class="text-gray-800">{{ $fasilitas }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Status Badge -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 font-semibold">Status</span>
                    @if($package->status === 'active')
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold">
                        <i class="fas fa-check-circle mr-1"></i> Active
                    </span>
                    @elseif($package->status === 'inactive')
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-bold">
                        <i class="fas fa-times-circle mr-1"></i> Inactive
                    </span>
                    @else
                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-bold">
                        <i class="fas fa-clock mr-1"></i> Under Review
                    </span>
                    @endif
                </div>
            </div>

            <!-- Contact -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-phone text-orange-500 mr-2"></i>
                    Your Contact
                </h3>

                <div class="space-y-3">
                    @if($package->whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $package->whatsapp) }}" target="_blank"
                       class="flex items-center gap-3 p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                        <i class="fas fa-whatsapp text-green-600 text-xl"></i>
                        <span class="text-gray-800">{{ $package->whatsapp }}</span>
                    </a>
                    @endif

                    @if($package->email)
                    <a href="mailto:{{ $package->email }}"
                       class="flex items-center gap-3 p-3 bg-red-50 rounded-lg hover:bg-red-100 transition">
                        <i class="fas fa-envelope text-red-600 text-xl"></i>
                        <span class="text-gray-800 text-sm">{{ $package->email }}</span>
                    </a>
                    @endif

                    @if($package->instagram)
                    <a href="https://instagram.com/{{ str_replace('@', '', $package->instagram) }}" target="_blank"
                       class="flex items-center gap-3 p-3 bg-pink-50 rounded-lg hover:bg-pink-100 transition">
                        <i class="fas fa-instagram text-pink-600 text-xl"></i>
                        <span class="text-gray-800">{{ $package->instagram }}</span>
                    </a>
                    @endif

                    @if($package->website)
                    <a href="{{ $package->website }}" target="_blank"
                       class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                        <i class="fas fa-globe text-blue-600 text-xl"></i>
                        <span class="text-gray-800 text-sm">{{ $package->website }}</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-xl shadow-lg p-6 space-y-3">
                <a href="{{ route('travel-agent.packages.edit', $package->id) }}"
                   class="block w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-3 rounded-lg font-bold text-center transition">
                    <i class="fas fa-edit mr-2"></i> Edit Package
                </a>

                <form method="POST" action="{{ route('travel-agent.packages.destroy', $package->id) }}" 
                      onsubmit="return confirm('Delete this package?');" style="display: inline-block; width: 100%;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-lg font-bold transition">
                        <i class="fas fa-trash mr-2"></i> Delete Package
                    </button>
                </form>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 mt-6">
                <p class="text-sm text-blue-900">
                    <i class="fas fa-lightbulb mr-2"></i>
                    <strong>Tips:</strong> Make sure all package information is complete and accurate before accepting participants.
                </p>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-8 text-center">
        <a href="{{ route('travel-agent.packages.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i> Back to Package List
        </a>
    </div>
</div>
@endsection