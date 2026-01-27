<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PemilikWisataController extends Controller
{
    /**
     * Dashboard pemilik wisata
     */
    public function dashboard()
    {
        $user = auth()->user();
        return view('pemilik.dashboard', compact('user'));
    }

    /**
     * Halaman profil pemilik wisata
     */
    public function profile()
    {
        $user = auth()->user();
        return view('pemilik.profile', compact('user'));
    }

    /**
     * Update profil pemilik wisata
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profil berhasil diupdate!');
    }
}