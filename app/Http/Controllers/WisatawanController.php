<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WisatawanController extends Controller
{
    /**
     * Halaman home wisatawan
     */
    public function beranda()
    {
        $user = auth()->user();
        return view('wisatawan.beranda', compact('user'));
    }

    /**
     * Halaman profil wisatawan
     */
    public function profile()
    {
        $user = auth()->user();
        return view('wisatawan.profile', compact('user'));
    }

    /**
     * Update profil wisatawan
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