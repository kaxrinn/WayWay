@extends('layouts.pemilik')

@section('title', 'Ajukan Edit Request')

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('pemilik.edit-request.index') }}" class="inline-flex items-center text-gray-600 hover:text-primary mb-6">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>
    
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-8 py-6">
            <h1 class="text-3xl font-bold text-white">📝 Ajukan Edit Request</h1>
            <p class="text-white/90 mt-2">Destinasi: {{ $destinasi->nama_destinasi }}</p>
        </div>
        
        <form method="POST" action="{{ route('pemilik.edit-request.store') }}" enctype="multipart/form-data" class="p-8">
            @csrf
            <input type="hidden" name="destinasi_id" value="{{ $destinasi->id }}">
            
            <div class="mb-6">
                <label class="block font-semibold mb-2">Tipe Request</label>
                <select name="request_type" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg">
                    <option value="">-- Pilih --</option>
                    <option value="edit_foto">Edit Foto</option>
                    <option value="add_foto">Tambah Foto</option>
                    <option value="delete_foto">Hapus Foto</option>
                    <option value="edit_info">Edit Info Destinasi</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label class="block font-semibold mb-2">Keterangan/Alasan</label>
                <textarea name="keterangan" rows="4" required placeholder="Jelaskan perubahan yang ingin Anda lakukan..."
                          class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg"></textarea>
            </div>
            
            <div class="mb-6">
                <label class="block font-semibold mb-2">Upload Foto (jika diperlukan)</label>
                <input type="file" name="foto[]" multiple accept="image/*" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg">
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4 rounded-lg font-semibold">
                <i class="fas fa-paper-plane mr-2"></i> Kirim Request
            </button>
        </form>
    </div>
</div>
@endsection