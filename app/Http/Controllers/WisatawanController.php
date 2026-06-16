<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use App\Models\Kategori;
use App\Models\HubungiKami;
use App\Models\Favorit;
use App\Models\Promosi;
use App\Models\TravelPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PartnerApplicationMail;
class WisatawanController extends Controller
{
    /**
     * Halaman home wisatawan
     */
    public function beranda()
    {
        $user     = auth()->user();
        $kategori = Kategori::all();


        // Ambil maksimal 6 destinasi per kategori, lalu gabungkan
        $destinasiPopuler = collect();

        foreach ($kategori as $kat) {
            $perKategori = Destinasi::with('kategori')
                ->withAvg('ulasan', 'rating')
                ->where('status', 'active')
                ->where('kategori_id', $kat->id)
                ->orderByDesc('ulasan_avg_rating')
                ->take(6)
                ->get();

            $destinasiPopuler = $destinasiPopuler->merge($perKategori);
        }
    
        // Hapus duplikat jika ada & re-index
        $destinasiPopuler = $destinasiPopuler->unique('id')->values();

        // Ambil favorit user yang login
        $favoritIds = [];
        if (auth()->check()) {
            $favoritIds = Favorit::where('user_id', auth()->id())
                ->pluck('destinasi_id')
                ->toArray();
        }

        // Ambil banner promosi aktif dari pemilik premium
        $iklanAktif = Promosi::whereNotNull('banner_promosi')
            ->aktif()   // scope: status=active & tanggal masih berlaku
            ->latest()
            ->get();

        // Ambil travel package aktif dan upcoming
        // Ambil paket travel
        $travelPackages = TravelPackage::with('travelAgent')
            ->where('status', 'active')
            ->whereDate('tanggal_keberangkatan', '>=', now())
            ->latest()
            ->take(6)
            ->get();
        return view('wisatawan.beranda', compact(
            'user',
            'destinasiPopuler',
            'kategori',
            'favoritIds',
            'iklanAktif',
            'travelPackages'
        ));
    }

    public function show(Destinasi $destinasi)
    {
        $destinasi->load(['kategori', 'ulasan.user']);

        $avgRating   = round($destinasi->ulasan()->avg('rating') ?? 5, 1);
        $totalReview = $destinasi->ulasan()->count();

        // Cek apakah destinasi ini sudah difavoritkan
        $isFavorited = false;
        if (auth()->check()) {
            $isFavorited = Favorit::where('user_id', auth()->id())
                ->where('destinasi_id', $destinasi->id)
                ->exists();
        }

        return view('wisatawan.berandasection.show', compact(
            'destinasi',
            'avgRating',
            'totalReview',
            'isFavorited'
        ));
    }

    public function index(Request $request)
    {
        $kategori  = Kategori::all();
        $destinasi = Destinasi::with('kategori')->where('status', 'active');

        // SEARCH
        if ($request->filled('q')) {
            $q = $request->q;
            $destinasi->where(function ($query) use ($q) {
                $query->where('nama_destinasi', 'like', "%$q%")
                      ->orWhere('deskripsi', 'like', "%$q%")
                      ->orWhereHas('kategori', function ($kat) use ($q) {
                          $kat->where('nama_kategori', 'like', "%$q%");
                      });
            });
        }

        // FILTER kategori
        if ($request->filled('kategori') && $request->kategori !== 'all') {
            $destinasi->where('kategori_id', $request->kategori);
        }

        // Ambil favorit user yang login
        $favoritIds = [];
        if (auth()->check()) {
            $favoritIds = Favorit::where('user_id', auth()->id())
                ->pluck('destinasi_id')
                ->toArray();
        }

        return view('wisatawan.berandasection.index', [
            'destinasi'     => $destinasi->paginate(9)->withQueryString(),
            'kategori'      => $kategori,
            'q'             => $request->q,
            'kategoriAktif' => $request->kategori,
            'favoritIds'    => $favoritIds,
        ]);
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
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'name'        => $request->name,
            'email'       => $request->email,
            'no_telepon'  => $request->no_telepon,
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return back()->with('success', 'profile has been updated successfully!');
    }

    // Hubungi Kami
    public function kirimPesan(Request $request)
    {
        $request->validate([
            'nama'   => 'required',
            'email'  => 'required|email',
            'subjek' => 'required',
            'pesan'  => 'required',
        ]);

        HubungiKami::create([
            'nama'    => $request->nama,
            'email'   => $request->email,
            'subjek'  => $request->subjek,
            'pesan'   => $request->pesan,
            'status'  => 'pending',
            'user_id' => auth()->id(),
        ]);

    return redirect()->to(url()->previous() . '#kontak')
        ->with('success', 'Pesan successfully sent!');
}

    /**
     * FAVORIT: Toggle add/remove favorit
     */
    public function toggle(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Please login first',
            ], 401);
        }

        $request->validate([
            'destinasi_id' => 'required|exists:destinasi,id',
        ]);

        $userId  = auth()->id();
        $favorit = Favorit::where('user_id', $userId)
            ->where('destinasi_id', $request->destinasi_id)
            ->first();

        if ($favorit) {
            $favorit->delete();
            return response()->json([
                'status'  => 'removed',
                'message' => 'Removed from favorites',
            ]);
        }

        Favorit::create([
            'user_id'      => $userId,
            'destinasi_id' => $request->destinasi_id,
        ]);

        return response()->json([
            'status'  => 'added',
            'message' => 'Added to favorites',
        ]);
    }

    /**
     * FAVORIT: Halaman daftar destinasi favorit
     */
    public function indexFavorit()
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login first to view favorites');
        }

        $userId = auth()->id();

        // Ambil destinasi yang difavoritkan user
        $destinasiFavorit = Destinasi::with(['kategori', 'ulasan'])
            ->whereHas('difavoritkanOleh', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->withAvg('ulasan', 'rating')
            ->where('status', 'active')
            ->latest()
            ->paginate(9);

        // Semua ID favorit untuk highlighting
        $favoritIds = Favorit::where('user_id', $userId)
            ->pluck('destinasi_id')
            ->toArray();

        return view('wisatawan.favorit.index', compact('destinasiFavorit', 'favoritIds'));
    }
   //paket dari agent travel 
public function travel($id)
{
    $package = TravelPackage::with('travelAgent')->findOrFail($id);

    return view(
        'wisatawan.berandasection.detailtravel',
        compact('package')
    );
}
// form bermitra
    public function form()
    {
        return view('wisatawan.berandasection.partner');
    }
 
    /**
     * Validate, send the application email to admin, and redirect home.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'partner_type'  => 'required|in:Destination Owner,Travel Agent',
            'name'          => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'whatsapp'      => 'required|string|max:20',
            'description'   => 'required|string|max:2000',
            'link'          => 'nullable|string|max:255',
        ]);
 
        Mail::to('waywaypolibatam@gmail.com')->send(new PartnerApplicationMail($validated));
 
        return redirect()
            ->route('wisatawan.beranda')
            ->with('success', 'Thank you! Your application has been sent. Our admin team will contact you via email soon.');
    }
}
