@extends('layouts.admin')

@section('title', 'Edit Request Details')

@section('content')
<a href="{{ route('admin.edit-requests.index') }}" 
   class="inline-flex items-center text-gray-600 hover:text-primary mb-6 transition">
    <i class="fas fa-arrow-left mr-2"></i> Back
</a>

<div class="bg-white rounded-xl shadow-xl overflow-hidden">
    <div class="bg-gradient-to-r from-primary to-blue-400 px-6 sm:px-8 py-5 sm:py-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-white">
            Edit Request Details #{{ $editRequest->id }}
        </h1>
    </div>
    
    <div class="p-6 sm:p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">User</label>
                <p class="text-lg font-bold">{{ $editRequest->user->name }}</p>
                <p class="text-sm text-gray-500">{{ $editRequest->user->email }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Destination</label>
                <p class="text-lg font-bold">{{ $editRequest->destinasi->nama_destinasi }}</p>
                <p class="text-sm text-gray-500">
                    {{ $editRequest->destinasi->kategori->nama_kategori ?? '-' }}
                </p>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Request Type</label>
                <p class="text-lg">
                    {{ ucfirst(str_replace('_', ' ', $editRequest->request_type)) }}
                </p>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Date</label>
                <p class="text-lg">{{ $editRequest->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-600 mb-2">User Description</label>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-700">{{ $editRequest->keterangan }}</p>
            </div>
        </div>
        
        @if($editRequest->request_data)
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-600 mb-2">Request Data</label>
            <div class="bg-gray-50 p-4 rounded-lg overflow-x-auto">
                <pre class="text-sm text-gray-700 whitespace-pre-wrap">
{{ json_encode($editRequest->request_data, JSON_PRETTY_PRINT) }}
                </pre>
            </div>
        </div>
        @endif
        
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-600 mb-2">Status</label>
            @if($editRequest->status == 'pending')
                <span class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full text-sm font-medium">
                    <i class="fas fa-clock mr-1"></i> Pending
                </span>
            @elseif($editRequest->status == 'approved')
                <span class="bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-medium">
                    <i class="fas fa-check-circle mr-1"></i> Approved
                </span>
            @else
                <span class="bg-red-100 text-red-800 px-4 py-2 rounded-full text-sm font-medium">
                    <i class="fas fa-times-circle mr-1"></i> Rejected
                </span>
            @endif
        </div>
        
        @if($editRequest->admin_notes)
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-600 mb-2">Admin Notes</label>
            <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-400">
                <p class="text-gray-700">{{ $editRequest->admin_notes }}</p>
                @if($editRequest->approvedBy)
                    <p class="text-sm text-gray-500 mt-2">
                        by: {{ $editRequest->approvedBy->name }} 
                        on {{ $editRequest->approved_at->format('d M Y H:i') }}
                    </p>
                @endif
            </div>
        </div>
        @endif
        
        @if($editRequest->status == 'pending')
        <div class="flex flex-col md:flex-row gap-4">
            <form method="POST" action="{{ route('admin.edit-requests.approve', $editRequest->id) }}" class="flex-1">
                @csrf
                <textarea name="admin_notes" placeholder="Notes (optional)..." 
                          class="w-full px-4 py-3 border-2 rounded-lg mb-3"></textarea>
                <button type="submit" 
                        class="w-full bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition">
                    <i class="fas fa-check mr-2"></i> Approve Request
                </button>
            </form>
            
            <form method="POST" action="{{ route('admin.edit-requests.reject', $editRequest->id) }}" class="flex-1">
                @csrf
                <textarea name="admin_notes" placeholder="Rejection reason (required)..." required 
                          class="w-full px-4 py-3 border-2 rounded-lg mb-3"></textarea>
                <button type="submit" 
                        class="w-full bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-semibold transition">
                    <i class="fas fa-times mr-2"></i> Reject Request
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection