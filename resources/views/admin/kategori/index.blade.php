@extends('layouts.admin')

@section('title', 'Manage Categories')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex flex-col sm:flex-row 
            sm:items-center sm:justify-between 
            gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i class="fas fa-tags text-green-500"></i>
                Manage Categories
            </h1>
            <p class="text-gray-500 mt-2">Manage tourism categories available on the platform</p>
        </div>
        <button onclick="openModal()" 
                class="bg-gradient-to-r from-green-500 to-green-600 
       text-white 
       w-full sm:w-auto
       text-sm sm:text-base
       px-4 sm:px-6 
       py-2 sm:py-3 
       rounded-lg 
       shadow-lg 
       flex items-center justify-center gap-2">
            <i class="fas fa-plus"></i>
            Add Category
        </button>
    </div>
</div>

<!-- Success Alert -->
@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
    <i class="fas fa-check-circle mr-2"></i>
    {{ session('success') }}
</div>
@endif

<!-- Category Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
    @forelse($kategori as $item)
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition flex flex-col justify-between">
            
            <!-- Top -->
            <div>
                <div class="flex items-start justify-between mb-4">
                    <div class="bg-green-500 p-4 rounded-xl">
                        <i class="fas fa-tag text-white text-2xl"></i>
                    </div>
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-bold">
                        {{ $item->destinasi_count }} Destinations
                    </span>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mb-2">
                    {{ $item->nama_kategori }}
                </h3>

                <p class="text-gray-600 text-sm mb-4">
                    {{ $item->deskripsi_kategori ?? '-' }}
                </p>
            </div>

            <!-- Action -->
            <div class="pt-4 border-t flex justify-end">
                <button
                     onclick="openEditModal({{ $item->id }})"
                     class="bg-blue-500 text-white px-3 py-1 rounded">
                       Edit
                </button>
            </div>
        </div>
    @empty
        <div class="col-span-3 text-center py-20 text-gray-400">
            <i class="fas fa-tags text-6xl mb-4"></i>
            <p>No categories yet</p>
        </div>
    @endforelse
</div>

<!-- Filter Destinations -->
<div class="bg-white rounded-xl shadow-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Filter Destinations by Category</h2>

    <select onchange="filterDestinasi(this.value)"
            class="w-full md:w-1/2 px-4 py-3 border-2 border-gray-300 rounded-lg">
        <option value="">All Categories</option>
        @foreach($kategori as $item)
            <option value="{{ $item->id }}">
                {{ $item->nama_kategori }} ({{ $item->destinasi_count }})
            </option>
        @endforeach
    </select>

    <div id="destinasiList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
        <div class="col-span-3 text-center text-gray-400 py-10">
            Select a category to view destinations
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div id="addKategoriModal"
     class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-8 w-full max-w-md">
        <h3 class="text-2xl font-bold mb-4">Add Category</h3>

        <form method="POST" action="{{ route('admin.kategori.store') }}">
            @csrf

            <input type="text"
                   name="nama_kategori"
                   placeholder="Category name"
                   class="w-full mb-4 px-4 py-3 border rounded-lg"
                   required>

            <textarea name="deskripsi_kategori"
                      placeholder="Description"
                      class="w-full mb-4 px-4 py-3 border rounded-lg"></textarea>

            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 bg-green-500 text-white py-2 rounded-lg">
                    Save
                </button>
                <button type="button"
                        onclick="closeModal()"
                        class="flex-1 bg-gray-500 text-white py-2 rounded-lg">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editKategoriModal"
     class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-8 w-full max-w-md">
        <h3 class="text-2xl font-bold mb-4">Edit Category</h3>

        <form id="editKategoriForm" method="POST">
            @csrf
            @method('PUT')

            <input type="text"
                   name="nama_kategori"
                   id="editNamaKategori"
                   class="w-full mb-4 px-4 py-3 border rounded-lg"
                   required>

            <textarea name="deskripsi_kategori"
                      id="editDeskripsiKategori"
                      class="w-full mb-4 px-4 py-3 border rounded-lg"></textarea>

            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 bg-green-500 text-white py-2 rounded-lg">
                    Save
                </button>
                <button type="button"
                        onclick="closeEditModal()"
                        class="flex-1 bg-gray-500 text-white py-2 rounded-lg">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ================= FILTER DESTINATIONS =================
async function filterDestinasi(kategoriId) {
    const el = document.getElementById('destinasiList');

    if (!kategoriId) {
        el.innerHTML = `
            <div class="col-span-3 text-center text-gray-400 py-10">
                Select a category to view destinations
            </div>`;
        return;
    }

    try {
        const res = await fetch(`/api/destinasi/kategori/${kategoriId}`);
        const data = await res.json();

        if (!data.length) {
            el.innerHTML = `
                <div class="col-span-3 text-center text-gray-400 py-10">
                    No destinations
                </div>`;
            return;
        }

        el.innerHTML = `
            <div class="col-span-3 overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="px-4 py-2 border">No</th>
                            <th class="px-4 py-2 border">Destination Name</th>
                            <th class="px-4 py-2 border">Description</th>
                            <th class="px-4 py-2 border text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.map((d, i) => `
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border">${i + 1}</td>
                                <td class="px-4 py-2 border font-semibold">${d.nama_destinasi}</td>
                                <td class="px-4 py-2 border text-sm text-gray-600">
                                    ${d.deskripsi ?? '-'}
                                </td>
                                <td class="px-4 py-2 border text-center">
                                    <a href="/admin/destinasi/${d.id}/edit"
                                       class="text-blue-500 hover:underline text-sm">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>`;
    } catch (err) {
        el.innerHTML = `
            <div class="col-span-3 text-center text-red-500 py-10">
                Failed to load destinations
            </div>`;
    }
}

// ================= MODAL ADD CATEGORY =================
function openModal() {
    document.getElementById('addKategoriModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('addKategoriModal').classList.add('hidden');
}

// ================= MODAL EDIT CATEGORY =================
function openEditModal(id) {
    fetch(`/admin/kategori/${id}/data`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('editNamaKategori').value = data.nama_kategori;
            document.getElementById('editDeskripsiKategori').value = data.deskripsi_kategori ?? '';
            document.getElementById('editKategoriForm').action = `/admin/kategori/${id}`;

            document.getElementById('editKategoriModal').classList.remove('hidden');
        });
}

function closeEditModal() {
    document.getElementById('editKategoriModal').classList.add('hidden');
}
</script>
@endpush