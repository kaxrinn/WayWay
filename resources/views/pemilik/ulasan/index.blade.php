@extends('layouts.pemilik')

@section('title', 'Destination Reviews')

@section('content')

<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-star text-yellow-400"></i>
            My Destination Reviews
        </h1>
        <p class="text-gray-500 mt-2 text-sm sm:text-base">View all reviews from visitors for your destinations</p>
    </div>
</div>

<!-- Stats Card -->
<div class="bg-gradient-to-r from-primary to-blue-400 rounded-xl p-4 sm:p-6 mb-6 text-white shadow-lg">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <div class="flex items-center gap-4">
            <div class="bg-white/20 p-3 sm:p-4 rounded-full flex-shrink-0">
                <i class="fas fa-comments text-2xl sm:text-3xl"></i>
            </div>
            <div>
                <p class="text-sm text-white/80">Total Reviews</p>
                <p class="text-2xl sm:text-3xl font-bold">{{ $stats['total'] }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="bg-white/20 p-3 sm:p-4 rounded-full flex-shrink-0">
                <i class="fas fa-star text-2xl sm:text-3xl"></i>
            </div>
            <div>
                <p class="text-sm text-white/80">Average Rating</p>
                <p class="text-2xl sm:text-3xl font-bold">{{ number_format($stats['rata_rata'], 1) }} / 5</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="bg-white/20 p-3 sm:p-4 rounded-full flex-shrink-0">
                <i class="fas fa-trophy text-2xl sm:text-3xl"></i>
            </div>
            <div>
                <p class="text-sm text-white/80">5-Star Reviews</p>
                <p class="text-2xl sm:text-3xl font-bold">{{ $stats['bintang5'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow-lg p-4 mb-6 flex flex-col md:flex-row gap-3">
    <input type="text" id="searchInput" placeholder="Search visitor name or comment..."
           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">

    <select id="filterDestinasiInput" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
        <option value="">All Destinations</option>
        @foreach($destinasiList as $d)
            <option value="{{ $d->id }}">{{ $d->nama_destinasi }}</option>
        @endforeach
    </select>

    <select id="filterRatingInput" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
        <option value="">All Ratings</option>
        @for($i = 5; $i >= 1; $i--)
            <option value="{{ $i }}">{{ str_repeat('⭐', $i) }} ({{ $i }})</option>
        @endfor
    </select>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    @if($ulasanList->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[600px]" id="ulasanTable">
            <thead class="bg-gradient-to-r from-primary to-blue-400 text-white">
                <tr>
                    <th class="px-4 sm:px-6 py-4 text-left font-semibold">Visitor</th>
                    <th class="px-4 sm:px-6 py-4 text-left font-semibold">Destination</th>
                    <th class="px-4 sm:px-6 py-4 text-left font-semibold">Rating</th>
                    <th class="px-4 sm:px-6 py-4 text-left font-semibold">Comment</th>
                    <th class="px-4 sm:px-6 py-4 text-left font-semibold">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($ulasanList as $ulasan)
                <tr class="hover:bg-accent/30 transition ulasan-row"
                    data-destinasi="{{ $ulasan->destinasi_id }}"
                    data-rating="{{ $ulasan->rating }}">

                    <td class="px-4 sm:px-6 py-4 font-medium text-gray-800">
                        {{ $ulasan->user->name ?? 'Anonymous' }}
                    </td>

                    <td class="px-4 sm:px-6 py-4 text-gray-700">
                        {{ $ulasan->destinasi->nama_destinasi ?? 'Unknown' }}
                    </td>

                    <td class="px-4 sm:px-6 py-4">
                        <div class="flex gap-0.5 items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-xs {{ $i <= $ulasan->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                            @endfor
                            <span class="ml-1 text-gray-500 text-xs">({{ $ulasan->rating }})</span>
                        </div>
                    </td>

                    <td class="px-4 sm:px-6 py-4 text-gray-600 max-w-xs">
                        @if($ulasan->komentar)
                            {{ Str::limit($ulasan->komentar, 70) }}
                        @else
                            <span class="text-gray-400 italic">No comment</span>
                        @endif
                    </td>

                    <td class="px-4 sm:px-6 py-4 text-gray-500 whitespace-nowrap">
                        {{ $ulasan->created_at->format('d M Y') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-4 sm:px-6 py-4 border-t bg-gray-50 flex justify-center">
        {{ $ulasanList->links() }}
    </div>

    @else
    <div class="py-16 sm:py-20 px-6 text-center">
        <div class="flex flex-col items-center justify-center text-gray-400">
            <i class="fas fa-star text-6xl sm:text-7xl mb-5"></i>
            <h3 class="text-xl sm:text-2xl font-bold text-gray-600 mb-2">No Reviews Yet</h3>
            <p class="text-gray-500 text-sm sm:text-base">Reviews from visitors will appear here</p>
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('filterDestinasiInput').addEventListener('change', filterTable);
document.getElementById('filterRatingInput').addEventListener('change', filterTable);

function filterTable() {
    const search    = document.getElementById('searchInput').value.toLowerCase();
    const destinasi = document.getElementById('filterDestinasiInput').value;
    const rating    = document.getElementById('filterRatingInput').value;

    document.querySelectorAll('.ulasan-row').forEach(row => {
        const text     = row.innerText.toLowerCase();
        const okSearch = !search    || text.includes(search);
        const okDest   = !destinasi || row.dataset.destinasi === destinasi;
        const okRating = !rating    || row.dataset.rating === rating;
        row.style.display = (okSearch && okDest && okRating) ? '' : 'none';
    });
}
</script>
@endpush