@extends('layouts.admin')

@section('title', 'Kelola Edit Requests')

@section('content')
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h1 class="text-3xl font-bold text-gray-800">📝 Kelola Edit Requests</h1>
    <p class="text-gray-500 mt-2">Approve atau reject request dari Basic users</p>
</div>

@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
</div>
@endif

<!-- Stats -->
<div class="grid grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl p-6 shadow-lg">
        <p class="text-gray-500 mb-1">Tunggu</p>
        <p class="text-3xl font-bold text-yellow-500">{{ $stats['pending'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-lg">
        <p class="text-gray-500 mb-1">Diterima</p>
        <p class="text-3xl font-bold text-green-500">{{ $stats['approved'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-lg">
        <p class="text-gray-500 mb-1">Ditolak</p>
        <p class="text-3xl font-bold text-red-500">{{ $stats['rejected'] }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    @if($requests->count() > 0)
    <table class="w-full">
        <thead class="bg-gradient-to-r from-primary to-blue-400 text-white">
            <tr>
                <th class="px-6 py-4 text-left">User</th>
                <th class="px-6 py-4 text-left">Destinasi</th>
                <th class="px-6 py-4 text-left">Tipe</th>
                <th class="px-6 py-4 text-left">Tanggal</th>
                <th class="px-6 py-4 text-left">Status</th>
                <th class="px-6 py-4 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($requests as $req)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">{{ $req->user->name }}</td>
                <td class="px-6 py-4 font-semibold">{{ $req->destinasi->nama_destinasi }}</td>
                <td class="px-6 py-4">{{ ucfirst(str_replace('_', ' ', $req->request_type)) }}</td>
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
        {{-- Terima & Tolak hanya untuk pending --}}
        @if($req->status == 'pending')
            <form method="POST" action="{{ route('admin.edit-requests.approve', $req->id) }}">
                @csrf
                <button class="bg-green-500 text-white px-3 py-1 rounded text-xs">
                    Terima
                </button>
            </form>

            <button onclick="openRejectModal({{ $req->id }})"
                class="bg-red-500 text-white px-3 py-1 rounded text-xs">
                Tolak
            </button>
        @endif

        {{-- Hapus untuk semua status --}}
        <form method="POST"
              action="{{ route('admin.edit-requests.destroy', $req->id) }}"
              onsubmit="return confirm('Yakin mau hapus edit request ini?')">
            @csrf
            @method('DELETE')
            <button class="bg-orange-600 text-white px-3 py-1 rounded text-xs">
                Hapus
            </button>
        </form>
    </div>
</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="py-12 text-center text-gray-400">
        <i class="fas fa-inbox text-6xl mb-4"></i>
        <p>Belum ada edit request</p>
    </div>
    @endif
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-8 max-w-md" onclick="event.stopPropagation()">
        <h3 class="text-xl font-bold mb-4">Reject Request</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <textarea name="admin_notes" placeholder="Alasan reject..." required 
                      class="w-full px-4 py-3 border-2 rounded-lg mb-4"></textarea>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-red-500 text-white px-4 py-2 rounded">Reject</button>
                <button type="button" onclick="closeRejectModal()" class="flex-1 bg-gray-300 px-4 py-2 rounded">Batal</button>
            </div>
        </form>
    </div>
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
</script>
@endpush