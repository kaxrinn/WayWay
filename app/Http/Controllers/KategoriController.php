<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display list kategori
     */
    public function index()
    {
        $kategori = Kategori::withCount('destinasi')->get();
        return view('admin.kategori.index', compact('kategori'));
    }

    /**
     * Store new kategori
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi_kategori' => 'nullable|string',
        ]);

        Kategori::create($request->all());

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Update kategori
     */
    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi_kategori' => 'nullable|string',
        ]);

        $kategori->update($request->all());

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Get kategori data for edit modal (AJAX)
     */
    public function getData(Kategori $kategori)
    {
        return response()->json($kategori);
    }
}