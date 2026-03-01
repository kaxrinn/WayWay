<?php

namespace App\Http\Controllers;

use App\Models\EditRequest;
use App\Models\Destinasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminEditRequestController extends Controller
{
    /**
     * Display all edit requests
     */
    public function index()
    {
        $requests = EditRequest::with(['user', 'destinasi.kategori', 'approvedBy'])
            ->orderByRaw("
                CASE status 
                    WHEN 'pending' THEN 1 
                    WHEN 'approved' THEN 2 
                    WHEN 'rejected' THEN 3 
                END
            ")
            ->latest()
            ->get();
        
        $stats = [
            'pending' => EditRequest::where('status', 'pending')->count(),
            'approved' => EditRequest::where('status', 'approved')->count(),
            'rejected' => EditRequest::where('status', 'rejected')->count(),
        ];
        
        return view('admin.edit-requests.index', compact('requests', 'stats'));
    }

    /**
     * Approve edit request
     */
    public function approve(Request $request, $id)
    {
        $editRequest = EditRequest::with('destinasi')->findOrFail($id);
        
        if ($editRequest->status !== 'pending') {
            return back()->with('error', 'This Request has already been processed!');
        }
        
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);
        
        // Apply changes to destinasi
        $destinasi = $editRequest->destinasi;
        $requestData = $editRequest->request_data;
        
        switch ($editRequest->request_type) {
            case 'edit_foto':
                // Update foto array
                if (isset($requestData['foto'])) {
                    $destinasi->foto = $requestData['foto'];
                    $destinasi->save();
                }
                break;
            
            case 'add_foto':
                // Add new foto to existing array
                if (isset($requestData['new_foto'])) {
                    $currentFoto = $destinasi->foto ?? [];
                    $destinasi->foto = array_merge($currentFoto, $requestData['new_foto']);
                    $destinasi->save();
                }
                break;
            
            case 'delete_foto':
                // Delete foto from array
                if (isset($requestData['foto_to_delete'])) {
                    $currentFoto = $destinasi->foto ?? [];
                    foreach ($requestData['foto_to_delete'] as $fotoPath) {
                        if (($key = array_search($fotoPath, $currentFoto)) !== false) {
                            Storage::disk('public')->delete($fotoPath);
                            unset($currentFoto[$key]);
                        }
                    }
                    $destinasi->foto = array_values($currentFoto);
                    $destinasi->save();
                }
                break;
            
            case 'edit_info':
                // Update basic info
                if (isset($requestData['nama_destinasi'])) {
                    $destinasi->nama_destinasi = $requestData['nama_destinasi'];
                }
                if (isset($requestData['deskripsi'])) {
                    $destinasi->deskripsi = $requestData['deskripsi'];
                }
                if (isset($requestData['harga'])) {
                    $destinasi->harga = $requestData['harga'];
                }
                $destinasi->save();
                break;
        }
        
        // Update request status
        $editRequest->update([
            'status' => 'approved',
            'admin_notes' => $validated['admin_notes'] ?? null,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        return back()->with('success', 'This Request has been approved and changes have been applied successfully!');
    }

    /**
     * Reject edit request
     */
    public function reject(Request $request, $id)
    {
        $editRequest = EditRequest::findOrFail($id);
        
        if ($editRequest->status !== 'pending') {
            return back()->with('error', 'Request ini sudah diproses!');
        }
        
        $validated = $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);
        
        $editRequest->update([
            'status' => 'rejected',
            'admin_notes' => $validated['admin_notes'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        return back()->with('success', 'This Request has been rejected successfully!');
    }

    /**
     * Show detail edit request
     */
    public function show($id)
    {
        $editRequest = EditRequest::with(['user', 'destinasi', 'approvedBy'])->findOrFail($id);
        
        return view('admin.edit-requests.show', compact('editRequest'));
    }

public function destroy($id)
{
    $request = \App\Models\EditRequest::findOrFail($id);
    $request->delete();

    return back()->with('success', 'Edit request has been deleted successfully!');
}


}