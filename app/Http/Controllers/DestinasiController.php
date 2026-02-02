<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DestinasiController extends Controller
{
    public function index()
    {
        $destinasi = Destinasi::with('kategori')->latest()->get();
        return view('admin.destinasi.index', compact('destinasi'));
    }

    public function create()
    {
        $kategori = Kategori::all();
        return view('admin.destinasi.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_destinasi' => 'required|string|max:255',
            'kategori_id'    => 'required',
            'harga'          => 'required|numeric|min:0',
            'latitude'       => 'required',
            'longitude'      => 'required',
            'deskripsi'      => 'required',
            'foto.*'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $fotoPaths = [];

        if ($request->hasFile('foto')) {

            if (count($request->file('foto')) > 3) {
                return back()->withErrors(['foto' => 'Maksimal upload 3 foto']);
            }

            foreach ($request->file('foto') as $file) {
                $path = $file->store('destinasi', 'public');
                $fotoPaths[] = $path;
            }
        }

        Destinasi::create([
            'nama_destinasi' => $request->nama_destinasi,
            'kategori_id'    => $request->kategori_id,
            'harga'          => $request->harga,
            'latitude'       => $request->latitude,
            'longitude'      => $request->longitude,
            'deskripsi'      => $request->deskripsi,
            'foto'           => json_encode($fotoPaths)
        ]);

        return redirect()->route('admin.destinasi.index')
            ->with('success', 'Destinasi berhasil ditambahkan');
    }

    public function edit($id)
    {
        $destinasi = Destinasi::findOrFail($id);
        $kategori = Kategori::all();
        return view('admin.destinasi.edit', compact('destinasi', 'kategori'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'nama_destinasi' => 'required|string|max:255',
        'kategori_id' => 'required',
        'harga' => 'required|numeric',
        'latitude' => 'required',
        'longitude' => 'required',
        'deskripsi' => 'required',
        'foto.*' => 'image|mimes:jpeg,png,jpg|max:2048'
    ]);

    $destinasi = Destinasi::findOrFail($id);

    $data = $request->except('foto');

    //  CEK JIKA ADA FOTO BARU
    if ($request->hasFile('foto')) {

        $files = $request->file('foto');

        // pastikan array
        if (!is_array($files)) {
            $files = [$files];
        }

        if (count($files) > 3) {
            return back()->withErrors(['foto' => 'Maksimal upload 3 foto']);
        }

        // hapus foto lama
        if ($destinasi->foto) {
            foreach (json_decode($destinasi->foto) as $oldFoto) {
                Storage::delete($oldFoto);
            }
        }

        $fotoPaths = [];
        foreach ($files as $file) {
            $path = $file->store('destinasi', 'public');
            $fotoPaths[] = $path;
        }

        $data['foto'] = json_encode($fotoPaths);
    }

    $destinasi->update($data);

    return redirect()->route('admin.destinasi.index')
        ->with('success', 'Destinasi berhasil diupdate');
}

    public function destroy($id)
    {
        $destinasi = Destinasi::findOrFail($id);

        if ($destinasi->foto) {
            $fotos = json_decode($destinasi->foto, true);
            foreach ($fotos as $foto) {
                if (Storage::disk('public')->exists($foto)) {
                    Storage::disk('public')->delete($foto);
                }
            }
        }

        $destinasi->delete();

        return redirect()->route('admin.destinasi.index')
            ->with('success', 'Destinasi berhasil dihapus');
    }
}
