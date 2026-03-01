<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Admin dashboard
     */
    public function dashboard()
    {
        $totalPemilik = User::where('role', 'pemilik_wisata')->count();
        $totalWisatawan = User::where('role', 'wisatawan')->count();
        
        return view('admin.dashboard', compact('totalPemilik', 'totalWisatawan'));
    }

    // ========== MANAGE TOURISM OWNERS ==========

    /**
     * Display list of tourism owners
     */
    public function indexPemilik()
    {
        $pemilikWisata = User::where('role', 'pemilik_wisata')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.pemilik.index', compact('pemilikWisata'));
    }

    /**
     * Show form to add a new tourism owner
     */
    public function createPemilik()
    {
        return view('admin.pemilik.create');
    }

    /**
     * Save new tourism owner
     */
    public function storePemilik(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
        ], [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',
            'email.unique' => 'Email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'password.regex' => 'Password must contain letters and numbers',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pemilik_wisata',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.pemilik.index')
            ->with('success', 'Tourism owner added successfully!');
    }

    /**
     * Show form to edit a tourism owner
     */
    public function editPemilik($id)
    {
        $pemilik = User::where('role', 'pemilik_wisata')->findOrFail($id);
        return view('admin.pemilik.edit', compact('pemilik'));
    }

    /**
     * Update tourism owner data
     */
    public function updatePemilik(Request $request, $id)
    {
        $pemilik = User::where('role', 'pemilik_wisata')->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|confirmed|regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
        ], [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',
            'email.unique' => 'Email is already registered',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'password.regex' => 'Password must contain letters and numbers',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pemilik->update($data);

        return redirect()->route('admin.pemilik.index')
            ->with('success', 'Tourism owner updated successfully!');
    }

    /**
     * Delete a tourism owner
     */
    public function destroyPemilik($id)
    {
        $pemilik = User::where('role', 'pemilik_wisata')->findOrFail($id);
        $pemilik->delete();

        return redirect()->route('admin.pemilik.index')
            ->with('success', 'Tourism owner deleted successfully!');
    }
}