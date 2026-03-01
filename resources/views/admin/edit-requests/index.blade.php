@extends('layouts.admin')

@section('title', 'Manage Edit Requests')

@section('content')
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h1 class="text-3xl font-bold text-gray-800">📝 Manage Edit Requests</h1>
    <p class="text-gray-500 mt-2">Approve or Reject requests from basic users</p>
</div>

@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
</div>
@endif

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl p-6 shadow-lg">
        <p class="text-gray-500 mb-1">Pending</p>
        <p class="text-3xl font-bold text-yellow-500">{{ $stats['pending'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-lg">
        <p class="text-gray-500 mb-1">Approved</p>
        <p class="text-3xl font-bold text-green-500">{{ $stats['approved'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-lg">
        <p class="text-gray-500 mb-1">Rejected</p>
        <p class="text-3xl font-bold text-red-500">{{ $stats['rejected'] }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
@if($requests->count() > 0)

<div class="overflow-x-auto">
<table class="w-full text-sm">
<thead class="bg-gradient-to-r from-primary to-blue-400 text-white">
<tr>
    <th class="px-6 py-4 text-left">User</th>
    <th class="px-6 py-4 text-left">Destination</th>
    <th class="px-6 py-4 text-left">Type</th>
    <th class="px-6 py-4 text-left">Photo Preview</th>
    <th class="px-6 py-4 text-left">Date</th>
    <th class="px-6 py-4 text-left">Status</th>
    <th class="px-6 py-4 text-left">Actions</th>
</tr>
</thead>

<tbody class="divide-y">
@foreach($requests as $req)
<tr class="hover:bg-gray-50">
    <td class="px-6 py-4">{{ $req->user->name }}</td>
    <td class="px-6 py-4 font-semibold">{{ $req->destinasi->nama_destinasi }}</td>
    <td class="px-6 py-4">{{ ucfirst(str_replace('_', ' ', $req->request_type)) }}</td>

    <!-- PHOTO PREVIEW -->
    <td class="px-6 py-4">
        @if(in_array($req->request_type, ['add_foto','edit_foto']) && !empty($req->request_data['new_foto']))
            <div class="flex gap-2 flex-wrap">
                @foreach($req->request_data['new_foto'] as $foto)
                    <img src="{{ asset('storage/'.$foto) }}"
                         class="w-16 h-16 object-cover rounded border shadow cursor-pointer"
                         onclick="openImageModal('{{ asset('storage/'.$foto) }}')">
                @endforeach
            </div>
        @else
            <span class="text-gray-400 italic">No photo</span>
        @endif
    </td>

    <td class="px-6 py-4 text-sm">{{ $req->created_at->format('d M Y') }}</td>

    <td class="px-6 py-4">
        @if($req->status == 'pending')
            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs">Pending</span>
        @elseif($req->status == 'approved')
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs">Approved</span>
        @else
            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs">Rejected</span>
        @endif
    </td>

    <td class="px-6 py-4">
        <div class="flex gap-2 flex-wrap">

        @if($req->status == 'pending')
            <form method="POST" action="{{ route('admin.edit-requests.approve', $req->id) }}">
                @csrf
                <button class="h-8 px-3 min-w-[80px] text-xs font-semibold rounded-md bg-green-500 hover:bg-green-600 text-white">
                    Approve
                </button>
            </form>

            <button onclick="openRejectModal({{ $req->id }})"
                class="h-8 px-3 min-w-[70px] text-xs font-semibold rounded-md bg-red-500 hover:bg-red-600 text-white">
                Reject
            </button>
        @endif

        <form method="POST"
              action="{{ route('admin.edit-requests.destroy', $req->id) }}"
              onsubmit="return confirm('Are you sure you want to delete this edit request?')">
            @csrf
            @method('DELETE')
            <button class="h-8 px-3 min-w-[70px] text-xs font-semibold rounded-md bg-orange-500 hover:bg-orange-600 text-white">
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
<div class="py-12 text-center text-gray-400">
    <i class="fas fa-inbox text-6xl mb-4"></i>
    <p>No edit requests yet</p>
</div>
@endif
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-8 max-w-md" onclick="event.stopPropagation()">
        <h3 class="text-xl font-bold mb-4">Reject Request</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <textarea name="admin_notes" placeholder="Reason for rejection..." required
                class="w-full px-4 py-3 border-2 rounded-lg mb-4"></textarea>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 h-10 bg-red-500 hover:bg-red-600 text-white rounded-md">
                    Reject
                </button>
                <button type="button" onclick="closeRejectModal()"
                    class="flex-1 h-10 bg-gray-400 hover:bg-gray-500 text-white rounded-md">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Image Preview Modal -->
<div id="imageModal" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center" onclick="closeImageModal()">
    <img id="modalImage" class="max-w-3xl max-h-[80vh] rounded shadow-lg border-4 border-white">
</div>

@endsection

@push('scripts')
<script>
function openRejectModal(id) {
    document.getElementById('rejectForm').action = `/admin/edit-requests/${id}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}
</script>
@endpush