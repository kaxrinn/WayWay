@extends('layouts.pemilik')

@section('title', 'Submit Edit Request')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-0">
    <a href="{{ route('pemilik.edit-request.index') }}" 
       class="inline-flex items-center text-gray-600 hover:text-primary mb-6 transition">
        <i class="fas fa-arrow-left mr-2"></i> Back
    </a>
    
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 sm:px-8 py-5 sm:py-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-white">📝 Submit Edit Request</h1>
            <p class="text-white/90 mt-2 text-sm sm:text-base">
                Destination: {{ $destinasi->nama_destinasi }}
            </p>
        </div>
        
        <form method="POST" action="{{ route('pemilik.edit-request.store') }}" enctype="multipart/form-data" 
              class="p-6 sm:p-8 space-y-6">
            @csrf
            <input type="hidden" name="destinasi_id" value="{{ $destinasi->id }}">
            
            <div>
                <label class="block font-semibold mb-2">Request Type</label>
                <select name="request_type" required 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring focus:ring-blue-200">
                    <option value="">-- Select --</option>
                    <option value="edit_foto">Edit Photo</option>
                    <option value="add_foto">Add Photo</option>
                    <option value="delete_foto">Delete Photo</option>
                    <option value="edit_info">Edit Destination Info</option>
                </select>
            </div>
            
            <div>
                <label class="block font-semibold mb-2">Description / Reason</label>
                <textarea name="keterangan" rows="4" required 
                          placeholder="Explain the changes you want to make..."
                          class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring focus:ring-blue-200"></textarea>
            </div>
            
            <div>
                <label class="block font-semibold mb-2">Upload Photo (if needed)</label>
                <input type="file" name="foto[]" multiple accept="image/*" 
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg">
                <p class="text-sm text-gray-500 mt-1">You can upload multiple images.</p>
            </div>
            
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 
                           text-white px-6 py-3 sm:py-4 rounded-lg font-semibold transition shadow-lg hover:shadow-xl">
                <i class="fas fa-paper-plane mr-2"></i> Submit Request
            </button>
        </form>
    </div>
</div>
@endsection