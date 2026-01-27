<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Dashboard admin
     */
    public function dashboard()
    {
        $totalPemilik = User::where('role', 'pemilik_wisata')->count();
        $totalWisatawan = User::where('role', 'wisatawan')->count();
        
        return view('admin.dashboard', compact('totalPemilik', 'totalWisatawan'));
    }

    // ========== KELOLA PEMILIK WISATA ==========
    
    /**
     * Tampilkan daftar pemilik wisata
     */
    public function indexPemilik()
    {
        $pemilikWisata = User::where('role', 'pemilik_wisata')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.pemilik.index', compact('pemilikWisata'));
    }

    /**
     * Tampilkan form tambah pemilik wisata
     */
    public function createPemilik()
    {
        return view('admin.pemilik.create');
    }

    /**
     * Simpan pemilik wisata baru
     */
    public function storePemilik(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.regex' => 'Password harus mengandung huruf dan angka',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pemilik_wisata',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.pemilik.index')
            ->with('success', 'Pemilik wisata berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit pemilik wisata
     */
    public function editPemilik($id)
    {
        $pemilik = User::where('role', 'pemilik_wisata')->findOrFail($id);
        return view('admin.pemilik.edit', compact('pemilik'));
    }

    /**
     * Update data pemilik wisata
     */
    public function updatePemilik(Request $request, $id)
    {
        $pemilik = User::where('role', 'pemilik_wisata')->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|confirmed|regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.regex' => 'Password harus mengandung huruf dan angka',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pemilik->update($data);

        return redirect()->route('admin.pemilik.index')
            ->with('success', 'Pemilik wisata berhasil diupdate!');
    }

    /**
     * Hapus pemilik wisata
     */
    public function destroyPemilik($id)
    {
        $pemilik = User::where('role', 'pemilik_wisata')->findOrFail($id);
        $pemilik->delete();

        return redirect()->route('admin.pemilik.index')
            ->with('success', 'Pemilik wisata berhasil dihapus!');
    }
}