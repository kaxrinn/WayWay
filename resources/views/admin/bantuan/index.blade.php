@extends('layouts.admin')

@section('title', 'Kelola Bantuan')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-life-ring text-blue-500"></i>
            Kelola Bantuan (Hubungi Kami)
        </h1>
        <p class="text-gray-500 mt-2">Manage pesan bantuan dan pertanyaan dari pengguna</p>
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

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-6 shadow-lg text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-yellow-100 text-sm mb-1">Pending</p>
                <h3 class="text-4xl font-bold">{{ $stats['pending'] }}</h3>
            </div>
            <div class="bg-white/20 p-4 rounded-full">
                <i class="fas fa-clock text-3xl"></i>
            </div>
        </div>
        <p class="text-yellow-100 text-sm">Menunggu ditangani</p>
    </div>
    
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 shadow-lg text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-blue-100 text-sm mb-1">Diproses</p>
                <h3 class="text-4xl font-bold">{{ $stats['processed'] }}</h3>
            </div>
            <div class="bg-white/20 p-4 rounded-full">
                <i class="fas fa-cog text-3xl"></i>
            </div>
        </div>
        <p class="text-blue-100 text-sm">Sedang ditangani</p>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 shadow-lg text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-green-100 text-sm mb-1">Selesai</p>
                <h3 class="text-4xl font-bold">{{ $stats['resolved'] }}</h3>
            </div>
            <div class="bg-white/20 p-4 rounded-full">
                <i class="fas fa-check-circle text-3xl"></i>
            </div>
        </div>
        <p class="text-green-100 text-sm">Sudah diselesaikan</p>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="relative">
            <input type="text" 
                   id="searchInput"
                   placeholder="🔍 Cari pesan..."
                   class="w-full px-6 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition pl-12">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
        
        <select id="filterStatus" class="px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition">
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="processed">Diproses</option>
            <option value="resolved">Selesai</option>
        </select>
    </div>
</div>

<!-- Messages Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    @if($messages->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full" id="bantuanTable">
            <thead class="bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold">ID</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Nama</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Email</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Subjek</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Tanggal</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($messages as $message)
                <tr class="hover:bg-accent/30 transition message-row" data-status="{{ $message->status }}">
                    <td class="px-6 py-4 text-sm font-medium text-gray-700">{{ $message->id }}</td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-800">{{ $message->nama }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $message->email }}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-800">{{ $message->subjek }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ Str::limit($message->pesan, 50) }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $message->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'pending' => 'yellow',
                                'processed' => 'blue',
                                'resolved' => 'green'
                            ];
                            $statusText = [
                                'pending' => 'Pending',
                                'processed' => 'Diproses',
                                'resolved' => 'Selesai'
                            ];
                            $color = $statusColors[$message->status] ?? 'gray';
                        @endphp
                        <span class="bg-{{ $color }}-100 text-{{ $color }}-800 px-3 py-1 rounded-full text-xs font-medium">
                            {{ $statusText[$message->status] ?? $message->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
    <div class="flex items-center gap-2">

        <!-- LIHAT -->
        <button onclick="viewMessage({{ json_encode($message) }})"
            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">
            <i class="fas fa-eye mr-1"></i> Lihat
        </button>

        <!-- UPDATE STATUS (HANYA JIKA BELUM RESOLVED) -->
        @if($message->status !== 'resolved')
            <form method="POST"
                  action="{{ route('admin.bantuan.update-status', $message->id) }}"
                  class="inline">
                @csrf
                <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg transition text-sm">
                    <i class="fas fa-check mr-1"></i>
                    {{ $message->status === 'pending' ? 'Proses' : 'Selesaikan' }}
                </button>
            </form>
        @endif

        <!-- HAPUS (SELALU ADA) -->
        <form method="POST"
              action="{{ route('admin.bantuan.destroy', $message->id) }}"
              onsubmit="return confirm('Yakin mau hapus pesan ini?')"
              class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                <i class="fas fa-trash mr-1"></i> Hapus
            </button>
        </form>

    </div>
</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t bg-gray-50 flex justify-center">
    {{ $messages->links() }}
</div>
    </div>
    @else
    <div class="py-20 px-6 text-center">
        <div class="flex flex-col items-center justify-center text-gray-400">
            <i class="fas fa-inbox text-7xl mb-5"></i>
            <h3 class="text-2xl font-bold text-gray-600 mb-2">Belum Ada Pesan</h3>
            <p class="text-gray-500">Belum ada pesan bantuan dari pengguna</p>
        </div>
    </div>
    @endif
</div>

<!-- View Message Modal -->
<div id="viewMessageModal"
     class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">

    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
         onclick="event.stopPropagation()">

        <!-- Header -->
        <div class="sticky top-0 bg-gradient-to-r from-blue-500 to-blue-600 px-8 py-6 flex items-center justify-between">
            <h3 class="text-2xl font-bold text-white">Detail Pesan</h3>
            <button type="button"
                    onclick="closeMessageModal()"
                    class="text-white hover:text-gray-200 text-2xl leading-none">
                &times;
            </button>
        </div>

        <!-- Content -->
        <div id="messageContent" class="p-8">
            <!-- diisi JS -->
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function viewMessage(message) {
    const statusColors = {
        pending: 'yellow',
        processed: 'blue',
        resolved: 'green'
    };

    const statusText = {
        pending: 'Pending',
        processed: 'Diproses',
        resolved: 'Selesai'
    };

    const color = statusColors[message.status] || 'gray';
    const status = statusText[message.status] || message.status;

    const gmailLink =
    'https://mail.google.com/mail/?view=cm' +
    '&to=' + encodeURIComponent(message.email) +
    '&subject=' + encodeURIComponent('Re: ' + message.subjek);

    const content = `
        <div class="space-y-4">
            <div>
                <p class="text-sm font-semibold text-gray-700">Dari</p>
                <p class="text-gray-800 font-medium">${message.nama}</p>
                <p class="text-sm text-gray-500">${message.email}</p>
            </div>

            <div>
                <p class="text-sm font-semibold text-gray-700">Subjek</p>
                <p class="text-gray-800 font-medium">${message.subjek}</p>
            </div>

            <div>
                <p class="text-sm font-semibold text-gray-700">Pesan</p>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-gray-800 whitespace-pre-wrap">${message.pesan}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Tanggal</p>
                    <p class="text-gray-600">
                        ${new Date(message.created_at).toLocaleString('id-ID')}
                    </p>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-700">Status</p>
                    <span class="bg-${color}-100 text-${color}-800 px-3 py-1 rounded-full text-xs font-medium">
                        ${status}
                    </span>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200 flex justify-end">
                <a href="${gmailLink}"
   target="_blank"
   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition inline-flex items-center gap-2 font-medium">
    <i class="fas fa-envelope"></i>
    Balas via Gmail
</a>
            </div>
        </div>
    `;

    document.getElementById('messageContent').innerHTML = content;
    document.getElementById('viewMessageModal').classList.remove('hidden');
}

function closeMessageModal() {
    document.getElementById('viewMessageModal').classList.add('hidden');
}

// klik area hitam = tutup
document.getElementById('viewMessageModal').addEventListener('click', function () {
    closeMessageModal();
});
</script>
@endpush