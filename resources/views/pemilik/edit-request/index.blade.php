@extends('layouts.pemilik')

@section('title', 'Edit Requests')

@section('content')
<div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-3">
        <i class="fas fa-paper-plane text-blue-500"></i>
        My Edit Request History
    </h1>
    <p class="text-gray-500 mt-2">List of edit requests you have submitted to the admin</p>
</div>

@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow">
    <div class="flex items-center">
        <i class="fas fa-check-circle text-2xl mr-3"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
</div>
@endif

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    @if($requests->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm sm:text-base">
            <thead class="bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                <tr>
                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left font-semibold">ID</th>
                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left font-semibold">Destination</th>
                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left font-semibold">Request Type</th>
                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left font-semibold">Description</th>
                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left font-semibold">Date</th>
                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left font-semibold">Status</th>
                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left font-semibold">Admin Notes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($requests as $req)
                <tr class="hover:bg-accent/30 transition">
                    <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-700 font-medium">{{ $req->id }}</td>
                    <td class="px-4 sm:px-6 py-3 sm:py-4">
                        <div class="font-semibold text-gray-800">{{ $req->destinasi->nama_destinasi }}</div>
                        <div class="text-xs text-gray-500">{{ $req->destinasi->kategori->nama_kategori ?? '-' }}</div>
                    </td>
                    <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-700">
                        {{ ucfirst(str_replace('_', ' ', $req->request_type)) }}
                    </td>
                    <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 max-w-xs truncate">
                        {{ $req->keterangan }}
                    </td>
                    <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600">
                        {{ $req->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="px-4 sm:px-6 py-3 sm:py-4">
                        @if($req->status == 'pending')
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-medium">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        @elseif($req->status == 'approved')
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
                                <i class="fas fa-check-circle mr-1"></i> Approved
                            </span>
                        @else
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">
                                <i class="fas fa-times-circle mr-1"></i> Rejected
                            </span>
                        @endif
                    </td>
                    <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600">
                        @if($req->admin_notes)
                            <div class="max-w-xs truncate" title="{{ $req->admin_notes }}">
                                {{ $req->admin_notes }}
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="py-16 sm:py-20 px-6 text-center">
        <div class="flex flex-col items-center justify-center text-gray-400">
            <i class="fas fa-inbox text-6xl sm:text-7xl mb-5"></i>
            <h3 class="text-xl sm:text-2xl font-bold text-gray-600 mb-2">No Requests Yet</h3>
            <p class="text-gray-500">You have not submitted any edit requests</p>
            
            <a href="{{ route('pemilik.destinasi.index') }}" 
               class="mt-6 bg-primary hover:bg-blue-500 text-white px-6 py-3 rounded-lg transition shadow-lg inline-flex items-center gap-2">
                <i class="fas fa-map-marked-alt"></i>
                View My Destinations
            </a>
        </div>
    </div>
    @endif
</div>

<!-- Info Box -->
<div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mt-6">
    <p class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
        <i class="fas fa-info-circle text-blue-600"></i>
        About Edit Requests:
    </p>
    <ul class="text-sm text-gray-600 space-y-1 ml-6">
        <li>• <strong>Basic</strong> plan cannot edit destinations directly</li>
        <li>• Requests will be processed by admin within 1–2 business days</li>
        <li>• If approved, changes will be applied automatically</li>
        <li>• Upgrade to <strong>Standard/Premium</strong> to edit directly without approval</li>
    </ul>
    
    <a href="{{ route('pemilik.paket.index') }}" 
       class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm transition">
        <i class="fas fa-rocket mr-1"></i> View Plans
    </a>
</div>
@endsection