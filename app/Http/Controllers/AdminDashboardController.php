<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_destinasi' => Destinasi::where('status', 'active')->count(),
            'total_kategori' => Kategori::count(),
            'total_wisatawan' => User::where('role', 'wisatawan')->count(),
            'total_pemilik' => User::where('role', 'pemilik_wisata')->count(),
        ];

        $recent_destinasi = Destinasi::with('kategori')
            ->where('status', 'active')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_destinasi'));
    }
}