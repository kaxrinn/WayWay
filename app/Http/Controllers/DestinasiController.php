<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DestinasiController extends Controller
{
    /**
     * Display list destinasi
     */
    public function index()
    {
        $destinasi = Destinasi::with('kategori')->latest()->get();
        return view('admin.destinasi.index', compact('destinasi'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $kategori = Kategori::all();
        return view('admin.destinasi.create', compact('kategori'));
    }

    /**
     * Store new destinasi
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_destinasi' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric|min:0',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'deskripsi' => 'required|string',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except('foto');
        $data['status'] = 'active';
        
        // Handle multiple foto upload
        $fotoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                // Store with correct path: storage/destinasi/foto/
                $path = $file->store('destinasi/foto', 'public');
                $fotoPaths[] = $path;
            }
        }
        
        $data['foto'] = $fotoPaths; // Will be cast to JSON by model

        Destinasi::create($data);

        return redirect()
            ->route('admin.destinasi.index')
            ->with('success', 'Destination created successfully!');
    }

    /**
     * Show edit form
     */
    public function edit(Destinasi $destinasi)
    {
        $kategori = Kategori::all();
        return view('admin.destinasi.edit', compact('destinasi', 'kategori'));
    }

    /**
     * Update destinasi
     */
    public function update(Request $request, Destinasi $destinasi)
    {
        $request->validate([
            'nama_destinasi' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric|min:0',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'deskripsi' => 'required|string',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'delete_foto' => 'nullable|array',
        ]);

        $data = $request->except(['foto', 'delete_foto']);
        
        // Get existing foto
        $existingFoto = $destinasi->foto ?? [];
        
        // Delete selected foto
        if ($request->has('delete_foto')) {
            foreach ($request->delete_foto as $index) {
                if (isset($existingFoto[$index])) {
                    Storage::disk('public')->delete($existingFoto[$index]);
                    unset($existingFoto[$index]);
                }
            }
            $existingFoto = array_values($existingFoto); // Re-index
        }
        
        // Add new foto
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $path = $file->store('destinasi/foto', 'public');
                $existingFoto[] = $path;
            }
        }
        
        $data['foto'] = $existingFoto;

        $destinasi->update($data);

        return redirect()
            ->route('admin.destinasi.index')
            ->with('success', 'Destination updated successfully!');
    }

    /**
     * Delete destinasi
     */
    public function destroy(Destinasi $destinasi)
    {
        // Delete all foto
        if ($destinasi->foto) {
            foreach ($destinasi->foto as $foto) {
                Storage::disk('public')->delete($foto);
            }
        }
        
        // Delete all video
        if ($destinasi->video) {
            foreach ($destinasi->video as $video) {
                Storage::disk('public')->delete($video);
            }
        }

        $destinasi->delete();

        return redirect()
            ->route('admin.destinasi.index')
            ->with('success', 'Destination deleted successfully!');
    }
}