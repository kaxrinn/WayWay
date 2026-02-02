<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Destinasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class PemilikWisataController extends Controller
{
    public function index()
    {
        $pemilikWisata = User::with('destinasi')
            ->where('role', 'pemilik_wisata')
            ->latest()
            ->get();

        return view('admin.pemilik.index', compact('pemilikWisata'));
    }

    public function create()
    {
        $destinasi = Destinasi::where('status', 'active')
            ->whereNull('user_id') //  belum punya pemilik
            ->orderBy('nama_destinasi')
            ->get();

        return view('admin.pemilik.create', compact('destinasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'destinasi_id' => 'required|exists:destinasi,id',
            'no_telepon' => 'nullable',
        ]);

        $pemilik = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pemilik_wisata',
            'no_telepon' => $request->no_telepon,
        ]);

        //  assign destinasi ke pemilik
        Destinasi::where('id', $request->destinasi_id)
            ->update(['user_id' => $pemilik->id]);

        return redirect()->route('admin.pemilik.index')
            ->with('success', 'Pemilik wisata berhasil ditambahkan');
    }

    public function edit(User $pemilik)
    {
        if ($pemilik->role !== 'pemilik_wisata') abort(404);

        $destinasi = Destinasi::where('status', 'active')
            ->where(function ($q) use ($pemilik) {
                $q->whereNull('user_id')
                  ->orWhere('user_id', $pemilik->id);
            })
            ->get();

        $pemilikWisata = User::with('destinasi')
    ->where('role', 'pemilik_wisata')
    ->latest()
    ->get();


        return view('admin.pemilik.edit', compact('pemilik', 'destinasi', 'pemilikWisata'));
    }

    public function update(Request $request, User $pemilik)
    {
        if ($pemilik->role !== 'pemilik_wisata') abort(404);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $pemilik->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'destinasi_id' => 'required|exists:destinasi,id',
            'no_telepon' => 'nullable',
        ]);

        $pemilik->update([
            'name' => $request->name,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon,
        ]);

        if ($request->filled('password')) {
            $pemilik->update([
                'password' => Hash::make($request->password)
            ]);
        }

        //  reset destinasi lama
        Destinasi::where('user_id', $pemilik->id)
            ->update(['user_id' => null]);

        //  assign destinasi baru
        Destinasi::where('id', $request->destinasi_id)
            ->update(['user_id' => $pemilik->id]);

        return redirect()->route('admin.pemilik.index')
            ->with('success', 'Data pemilik wisata berhasil diupdate');
    }

    public function destroy(User $pemilik)
    {
        if ($pemilik->role !== 'pemilik_wisata') abort(404);

        Destinasi::where('user_id', $pemilik->id)
            ->update(['user_id' => null]);

        $pemilik->delete();

        return redirect()->route('admin.pemilik.index')
            ->with('success', 'Pemilik wisata berhasil dihapus');
    }
}
