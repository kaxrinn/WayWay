<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Promosi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PemilikPromosiController extends Controller
{
    /**
     * Tampilkan halaman kelola banner promosi milik pemilik yang login.
     */
    public function index()
    {
        $user  = auth()->user();
        $paket = $user->currentPaket;

        // Hanya paket Premium (priority_level = 3) yang bisa akses
        if (!$paket || $paket->priority_level < 3) {
            return redirect()->route('pemilik.paket.index')
                ->with('error', 'This feature is only available for Premium Package. Please upgrade your package to access banner promotion features!');
        }

        // Cek apakah paket masih aktif (ada di tabel user_paket / cek via model)
        $promosi = Promosi::where('user_id', $user->id)
            ->latest()
            ->first();

        return view('pemilik.promosi.index', compact('promosi', 'paket', 'user'));
    }

    /**
     * Simpan atau update banner promosi.
     * Setiap pemilik premium hanya boleh punya 1 banner aktif per paket aktif.
     */
    public function store(Request $request)
    {
        $user  = auth()->user();
        $paket = $user->currentPaket;

        if (!$paket || $paket->priority_level < 3) {
            return redirect()->route('pemilik.paket.index')
                ->with('error', 'Fitur ini hanya tersedia untuk Paket Premium.');
        }

        $request->validate([
            'judul_banner'     => 'required|string|max:100',
            'deskripsi_banner' => 'nullable|string|max:500',
            'banner_promosi'   => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'judul_banner.required'   => 'Judul banner wajib diisi.',
            'banner_promosi.required' => 'Gambar banner wajib diupload.',
            'banner_promosi.image'    => 'File harus berupa gambar.',
            'banner_promosi.mimes'    => 'Format gambar harus JPG, PNG, atau WEBP.',
            'banner_promosi.max'      => 'Ukuran gambar maksimal 2MB.',
        ]);

        // Cek sudah ada promosi aktif belum
        $existing = Promosi::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active'])
            ->first();

        if ($existing) {
            return redirect()->route('pemilik.promosi.index')
                ->with('error', 'You already have an active banner promotion. Please delete or edit the existing one first.');
        }

        // Upload banner
        $path = $request->file('banner_promosi')->store('promosi/banner', 'public');

        // Ambil tanggal paket aktif user
        $userPaketAktif = $user->activePaket ?? null;
        $tanggalMulai   = now()->toDateString();
        $tanggalSelesai = $userPaketAktif
            ? $userPaketAktif->tanggal_selesai
            : now()->addDays($paket->durasi_hari ?? 30)->toDateString();

        Promosi::create([
            'user_id'          => $user->id,
            'destinasi_id'     => null,
            'paket_id'         => $paket->id,
            'judul_banner'     => $request->judul_banner,
            'deskripsi_banner' => $request->deskripsi_banner,
            'banner_promosi'   => $path,
            'tanggal_mulai'    => $tanggalMulai,
            'tanggal_selesai'  => $tanggalSelesai,
            'status'           => 'active',
        ]);

        return redirect()->route('pemilik.promosi.index')
            ->with('success', 'Banner promosi successfully uploaded!');
    }

    /**
     * Tampilkan form edit banner.
     */
    public function edit(Promosi $promosi)
    {
        $user = auth()->user();

        // Pastikan banner milik user ini
        if ($promosi->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $paket = $user->currentPaket;
        if (!$paket || $paket->priority_level < 3) {
            return redirect()->route('pemilik.paket.index')
                ->with('error', 'This feature is only available for Premium Package.');
        }

        return view('pemilik.promosi.edit', compact('promosi', 'paket'));
    }

    /**
     * Update banner promosi.
     */
    public function update(Request $request, Promosi $promosi)
    {
        $user = auth()->user();

        if ($promosi->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'judul_banner'     => 'required|string|max:100',
            'deskripsi_banner' => 'nullable|string|max:500',
            'banner_promosi'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'judul_banner.required' => 'Judul banner wajib diisi.',
            'banner_promosi.image'  => 'File harus berupa gambar.',
            'banner_promosi.mimes'  => 'Format gambar harus JPG, PNG, atau WEBP.',
            'banner_promosi.max'    => 'Ukuran gambar maksimal 2MB.',
        ]);

        $data = [
            'judul_banner'     => $request->judul_banner,
            'deskripsi_banner' => $request->deskripsi_banner,
        ];

        // Kalau ada upload gambar baru
        if ($request->hasFile('banner_promosi')) {
            // Hapus gambar lama
            if ($promosi->banner_promosi) {
                Storage::disk('public')->delete($promosi->banner_promosi);
            }
            $data['banner_promosi'] = $request->file('banner_promosi')->store('promosi/banner', 'public');
        }

        $promosi->update($data);

        return redirect()->route('pemilik.promosi.index')
            ->with('success', 'Banner promosi successfully updated!');
    }

    /**
     * Hapus banner promosi.
     */
    public function destroy(Promosi $promosi)
    {
        $user = auth()->user();

        if ($promosi->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        // Hapus file gambar dari storage
        if ($promosi->banner_promosi) {
            Storage::disk('public')->delete($promosi->banner_promosi);
        }

        $promosi->delete();

        return redirect()->route('pemilik.promosi.index')
            ->with('success', 'Banner promosi successfully deleted!');
    }
}