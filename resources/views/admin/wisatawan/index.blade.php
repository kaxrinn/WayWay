@extends('layouts.admin')

@section('title', 'Manage Tourists')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-users text-blue-500"></i>
                Manage Tourists
            </h1>
            <p class="text-gray-500 mt-2">List of all tourists registered in the system</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Total Tourists</p>
                <h3 class="text-3xl font-bold text-blue-500">{{ $wisatawan->count() }}</h3>
            </div>
            <div class="bg-blue-100 p-4 rounded-full">
                <i class="fas fa-users text-2xl text-blue-500"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">This Month</p>
                <h3 class="text-3xl font-bold text-green-500">{{ $wisatawan->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
            </div>
            <div class="bg-green-100 p-4 rounded-full">
                <i class="fas fa-user-plus text-2xl text-green-500"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Today</p>
                <h3 class="text-3xl font-bold text-purple-500">{{ $wisatawan->where('created_at', '>=', today())->count() }}</h3>
            </div>
            <div class="bg-purple-100 p-4 rounded-full">
                <i class="fas fa-calendar-day text-2xl text-purple-500"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search Box -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="relative">
        <input type="text" 
               id="searchInput"
               placeholder="🔍 Search tourists by name or email..."
               class="w-full px-6 py-4 border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/20 transition pl-12">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    @if($wisatawan->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full" id="wisatawanTable">
            <thead class="bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold">No</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Name</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Email</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Phone</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Registered</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($wisatawan as $index => $user)
                <tr class="hover:bg-accent/30 transition">
                    <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <span class="text-blue-600 font-bold text-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            </div>
                            <span class="font-semibold text-gray-800">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $user->no_telepon ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium flex items-center gap-1 w-fit">
                            <i class="fas fa-check-circle"></i>
                            Active
                        </span>
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
            <h3 class="text-2xl font-bold text-gray-600 mb-2">No Tourists Yet</h3>
            <p class="text-gray-500">No tourists are registered in the system</p>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const table = document.getElementById('wisatawanTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let row of rows) {
        const name = row.cells[1].textContent.toLowerCase();
        const email = row.cells[2].textContent.toLowerCase();
        
        if (name.includes(searchValue) || email.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});
</script>
@endpush