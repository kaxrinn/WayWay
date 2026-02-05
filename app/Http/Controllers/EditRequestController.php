<?php

namespace App\Http\Controllers;

use App\Models\EditRequest;
use App\Models\Destinasi;
use Illuminate\Http\Request;

class EditRequestController extends Controller
{
    /**
     * Display a listing of edit requests
     */
    public function index()
    {
        $requests = EditRequest::where('user_id', auth()->id())
            ->with(['destinasi', 'approvedBy'])
            ->latest()
            ->get();
        
        return view('pemilik.edit-request.index', compact('requests'));
    }

    /**
     * Show the form for creating a new edit request
     */
    public function create(Request $request)
    {
        $destinasiId = $request->query('destinasi_id');
        $destinasi = Destinasi::where('user_id', auth()->id())->findOrFail($destinasiId);
        
        return view('pemilik.edit-request.create', compact('destinasi'));
    }

    /**
     * Store a newly created edit request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'destinasi_id' => 'required|exists:destinasi,id',
            'request_type' => 'required|in:edit_foto,edit_info,delete_foto,add_foto',
            'request_data' => 'nullable|array',
            'keterangan' => 'required|string|max:500',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Check ownership
        $destinasi = Destinasi::where('user_id', auth()->id())->findOrFail($validated['destinasi_id']);
        
        $requestData = $validated['request_data'] ?? [];
        
        // Handle foto upload untuk add_foto request
        if ($validated['request_type'] == 'add_foto' && $request->hasFile('foto')) {
            $fotoArray = [];
            foreach ($request->file('foto') as $foto) {
                $path = $foto->store('edit-requests/foto', 'public');
                $fotoArray[] = $path;
            }
            $requestData['new_foto'] = $fotoArray;
        }
        
        EditRequest::create([
            'user_id' => auth()->id(),
            'destinasi_id' => $validated['destinasi_id'],
            'request_type' => $validated['request_type'],
            'request_data' => $requestData,
            'keterangan' => $validated['keterangan'],
            'status' => 'pending',
        ]);
        
        return redirect()->route('pemilik.edit-request.index')
            ->with('success', 'Request edit berhasil diajukan! Tunggu approval dari admin.');
    }
}