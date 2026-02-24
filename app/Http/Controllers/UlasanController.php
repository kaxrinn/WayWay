<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use App\Models\Kategori;
use App\Models\Ulasan;
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'destinasi_id' => 'required|exists:destinasi,id',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string',
        ]);

        Ulasan::create([
            'destinasi_id' => $request->destinasi_id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return back()->with('success', 'Ulasan berhasil dikirim!');
    }
public function indexPemilik()
{
    $pemilik = auth()->user();

    // Ambil semua destinasi milik pemilik ini
    $destinasiIds = \App\Models\Destinasi::where('user_id', $pemilik->id)
        ->pluck('id');

    // Ambil semua ulasan dari destinasi tersebut, dengan relasi
    $ulasanList = \App\Models\Ulasan::with(['destinasi', 'user'])
        ->whereIn('destinasi_id', $destinasiIds)
        ->latest()
        ->paginate(15);

    // Daftar destinasi untuk filter dropdown
$destinasiList = \App\Models\Destinasi::where('user_id', $pemilik->id)
    ->select('id', 'nama_destinasi')
    ->get();

    // Statistik ringkas
    $allUlasan = \App\Models\Ulasan::whereIn('destinasi_id', $destinasiIds);
    $stats = [
        'total'      => $allUlasan->count(),
        'rata_rata'  => $allUlasan->avg('rating') ?? 0,
        'bintang5'   => (clone $allUlasan)->where('rating', 5)->count(),
    ];

    return view('pemilik.ulasan.index', compact('ulasanList', 'destinasiList', 'stats'));
}
}
