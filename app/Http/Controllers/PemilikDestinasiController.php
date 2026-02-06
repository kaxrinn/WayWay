<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PemilikDestinasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $destinasi = $user->destinasi()->with('kategori')->latest()->get();
        
        $currentPaket = $user->currentPaket;
        
        // Auto-assign Basic if null
        if (!$currentPaket) {
            $basicPaket = \App\Models\PaketPromosi::where('nama_paket', 'Basic')->first();
            if ($basicPaket) {
                $user->update(['current_paket_id' => $basicPaket->id]);
                $currentPaket = $basicPaket;
            }
        }
        
        // Get limits with fallback
        $limits = $currentPaket ? [
            'max_destinasi' => $currentPaket->max_destinasi,
            'max_foto' => $currentPaket->max_foto,
            'max_video' => $currentPaket->max_video,
            'can_edit_foto' => $currentPaket->can_edit_foto,
            'is_featured_allowed' => $currentPaket->is_featured_allowed,
        ] : [
            'max_destinasi' => 1,
            'max_foto' => 3,
            'max_video' => 0,
            'can_edit_foto' => false,
            'is_featured_allowed' => false,
        ];
        
        return view('pemilik.destinasi.index', compact('destinasi', 'limits', 'currentPaket'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $limits = $user->getPaketLimits();
        
        // Check batasan destinasi
        $jumlahDestinasi = $user->destinasi()->count();
        $maxDestinasi = $limits['max_destinasi'] ?? PHP_INT_MAX;
        
        if ($jumlahDestinasi >= $maxDestinasi) {
            return redirect()->route('pemilik.destinasi.index')
                ->with('error', 'Batas destinasi sudah tercapai! Upgrade paket Anda untuk menambah lebih banyak destinasi.');
        }
        
        $kategori = Kategori::all();
        
        return view('pemilik.destinasi.create', compact('kategori', 'limits'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $limits = $user->getPaketLimits();
        
        // Check batasan destinasi
        $jumlahDestinasi = $user->destinasi()->count();
        $maxDestinasi = $limits['max_destinasi'] ?? PHP_INT_MAX;
        
        if ($jumlahDestinasi >= $maxDestinasi) {
            return back()->with('error', 'Batas destinasi sudah tercapai!');
        }
        
        $validated = $request->validate([
            'nama_destinasi' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'kategori_id' => 'required|exists:kategori,id',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'video.*' => 'nullable|mimes:mp4,mov,avi|max:10240', // 10MB max
        ]);
        
        $maxFoto = $limits['max_foto'] ?? PHP_INT_MAX;
        $maxVideo = $limits['max_video'] ?? PHP_INT_MAX;
        
        // Handle foto uploads
        $fotoArray = [];
        if ($request->hasFile('foto')) {
            $uploadedFoto = count($request->file('foto'));
            
            if ($uploadedFoto > $maxFoto) {
                return back()->with('error', "Maksimal $maxFoto foto untuk paket Anda!");
            }
            
            foreach ($request->file('foto') as $foto) {
                $path = $foto->store('destinasi/foto', 'public');
                $fotoArray[] = $path;
            }
        }
        
        // Handle video uploads
        $videoArray = [];
        if ($request->hasFile('video')) {
            $uploadedVideo = count($request->file('video'));
            
            if ($uploadedVideo > $maxVideo) {
                return back()->with('error', "Maksimal $maxVideo video untuk paket Anda!");
            }
            
            foreach ($request->file('video') as $video) {
                $path = $video->store('destinasi/video', 'public');
                $videoArray[] = $path;
            }
        }
        
        // Create destinasi
        Destinasi::create([
            'nama_destinasi' => $validated['nama_destinasi'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'deskripsi' => $validated['deskripsi'],
            'harga' => $validated['harga'],
            'kategori_id' => $validated['kategori_id'],
            'foto' => $fotoArray,
            'video' => $videoArray,
            'user_id' => $user->id,
            'status' => 'active',
            'is_featured' => false, // Default false
        ]);
        
        return redirect()->route('pemilik.destinasi.index')
            ->with('success', 'Destinasi berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $destinasi = Destinasi::where('user_id', auth()->id())
            ->with('kategori')
            ->findOrFail($id);
        
        return view('pemilik.destinasi.show', compact('destinasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        $limits = $user->getPaketLimits();
        $destinasi = Destinasi::where('user_id', $user->id)->findOrFail($id);
        
        // Check apakah bisa edit foto langsung
        if (!$limits['can_edit_foto']) {
            return redirect()->route('pemilik.edit-request.create', ['destinasi_id' => $id])
                ->with('info', 'Paket Basic tidak bisa edit langsung. Silakan ajukan request edit.');
        }
        
        $kategori = Kategori::all();
        
        return view('pemilik.destinasi.edit', compact('destinasi', 'kategori', 'limits'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        $limits = $user->getPaketLimits();
        $destinasi = Destinasi::where('user_id', $user->id)->findOrFail($id);
        
        // Check permission
        if (!$limits['can_edit_foto']) {
            return back()->with('error', 'Paket Basic tidak bisa edit langsung!');
        }
        
        $validated = $request->validate([
            'nama_destinasi' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'kategori_id' => 'required|exists:kategori,id',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'video.*' => 'nullable|mimes:mp4,mov,avi|max:10240',
            'is_featured' => 'nullable|boolean',
            'delete_foto' => 'nullable|array',
            'delete_video' => 'nullable|array',
        ]);
        
        $fotoArray = $destinasi->foto ?? [];
        $videoArray = $destinasi->video ?? [];
        
        // Handle delete foto
        if ($request->has('delete_foto')) {
            foreach ($request->delete_foto as $fotoPath) {
                if (($key = array_search($fotoPath, $fotoArray)) !== false) {
                    Storage::disk('public')->delete($fotoPath);
                    unset($fotoArray[$key]);
                }
            }
            $fotoArray = array_values($fotoArray); // Re-index
        }
        
        // Handle delete video
        if ($request->has('delete_video')) {
            foreach ($request->delete_video as $videoPath) {
                if (($key = array_search($videoPath, $videoArray)) !== false) {
                    Storage::disk('public')->delete($videoPath);
                    unset($videoArray[$key]);
                }
            }
            $videoArray = array_values($videoArray);
        }
        
        // Handle new foto uploads
        if ($request->hasFile('foto')) {
            $maxFoto = $limits['max_foto'] ?? PHP_INT_MAX;
            $currentFotoCount = count($fotoArray);
            $newFotoCount = count($request->file('foto'));
            
            if (($currentFotoCount + $newFotoCount) > $maxFoto) {
                return back()->with('error', "Maksimal $maxFoto foto untuk paket Anda!");
            }
            
            foreach ($request->file('foto') as $foto) {
                $path = $foto->store('destinasi/foto', 'public');
                $fotoArray[] = $path;
            }
        }
        
        // Handle new video uploads
        if ($request->hasFile('video')) {
            $maxVideo = $limits['max_video'] ?? PHP_INT_MAX;
            $currentVideoCount = count($videoArray);
            $newVideoCount = count($request->file('video'));
            
            if (($currentVideoCount + $newVideoCount) > $maxVideo) {
                return back()->with('error', "Maksimal $maxVideo video untuk paket Anda!");
            }
            
            foreach ($request->file('video') as $video) {
                $path = $video->store('destinasi/video', 'public');
                $videoArray[] = $path;
            }
        }
        
        // Handle is_featured (only for Premium)
        $isFeatured = false;
        if ($limits['is_featured_allowed'] && $request->has('is_featured')) {
            $isFeatured = (bool) $request->is_featured;
        }
        
        // Update destinasi
        $destinasi->update([
            'nama_destinasi' => $validated['nama_destinasi'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'deskripsi' => $validated['deskripsi'],
            'harga' => $validated['harga'],
            'kategori_id' => $validated['kategori_id'],
            'foto' => $fotoArray,
            'video' => $videoArray,
            'is_featured' => $isFeatured,
        ]);
        
        return redirect()->route('pemilik.destinasi.index')
            ->with('success', 'Destinasi berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $destinasi = Destinasi::where('user_id', auth()->id())->findOrFail($id);
        
        // Soft delete by changing status
        $destinasi->update(['status' => 'inactive']);
        
        return redirect()->route('pemilik.destinasi.index')
            ->with('success', 'Destinasi berhasil dihapus!');
    }
}