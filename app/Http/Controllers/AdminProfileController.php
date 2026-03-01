<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    public function updateProfile(Request $request)
{
    $admin = auth()->user();

    if ($admin->role !== 'admin') {
        abort(403);
    }

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $admin->id,
        'no_telepon' => 'nullable|string|max:20',
        'password' => 'nullable|min:8|confirmed',
    ]);

    $admin->update([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'no_telepon' => $validated['no_telepon'] ?? $admin->no_telepon,
    ]);

    // ⬇️ PASSWORD HANYA DIUPDATE JIKA BENAR-BENAR DIISI
    if ($request->filled('password')) {
        $admin->password = Hash::make($validated['password']);
        $admin->save();
    }

    return back()->with('success', 'Profil admin has been updated successfully!');
}
}
