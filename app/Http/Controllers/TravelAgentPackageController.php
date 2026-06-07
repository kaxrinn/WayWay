<?php

namespace App\Http\Controllers;

use App\Models\TravelPackage;
use App\Models\TravelAgentSubscription;
use Illuminate\Http\Request;

class TravelAgentPackageController extends Controller
{
    /**
     * List paket wisata milik travel agent
     */
    public function index()
    {
        $userId = auth()->id();
        $packages = TravelPackage::where('travel_agent_id', $userId)
            ->latest()
            ->paginate(10);

        // Get active subscription untuk tau limit max packages
        $activeSubscription = TravelAgentSubscription::where('travel_agent_id', $userId)
            ->with('package')
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expired_at')
                  ->orWhere('expired_at', '>', now());
            })
            ->latest('started_at')
            ->first();

        $maxPackages = $activeSubscription?->package->max_packages ?? 1; // Default basic = 1
        $currentPackages = $packages->total();

        return view('travel-agent.packages.index', compact('packages', 'maxPackages', 'currentPackages'));
    }

    /**
     * Create paket form
     */
    public function create()
    {
        $userId = auth()->id();
        $activeSubscription = TravelAgentSubscription::where('travel_agent_id', $userId)
            ->with('package')
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expired_at')
                  ->orWhere('expired_at', '>', now());
            })
            ->latest('started_at')
            ->first();

        $maxPackages = $activeSubscription?->package->max_packages ?? 1;
        $currentPackages = TravelPackage::where('travel_agent_id', $userId)->count();

        // Check apakah bisa buat paket baru
        if ($currentPackages >= $maxPackages) {
            return redirect()->route('travel-agent.packages.index')
                ->with('error', 'Anda sudah mencapai limit paket! Upgrade paket untuk menambah lebih banyak.');
        }

        return view('travel-agent.packages.create');
    }

    /**
     * Store paket baru
     */
    public function store(Request $request)
    {
        $userId = auth()->id();

        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'harga_per_orang' => 'required|numeric|min:0',
            'durasi_hari' => 'required|integer|min:1',
            'tanggal_keberangkatan' => 'required|date|after:today',
            'destinasi' => 'required|array|min:1',
            'fasilitas_include' => 'nullable|array',
            'fasilitas_exclude' => 'nullable|array',
            'itinerary' => 'nullable|array',
            'min_peserta' => 'required|integer|min:1',
            'max_peserta' => 'required|integer|min:1',
            'meeting_point' => 'required|string',
            'whatsapp' => 'nullable|string',
            'email' => 'nullable|email',
            'instagram' => 'nullable|string',
            'website' => 'nullable|url',
        ]);

        // Handle thumbnail
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('travel-packages', 'public');
        }

        $validated['travel_agent_id'] = $userId;

        TravelPackage::create($validated);

        return redirect()->route('travel-agent.packages.index')
            ->with('success', 'Paket wisata berhasil dibuat!');
    }

    /**
     * Edit paket form
     */
    public function edit($id)
    {
        $package = TravelPackage::where('travel_agent_id', auth()->id())->findOrFail($id);
        return view('travel-agent.packages.edit', compact('package'));
    }

    /**
     * Update paket
     */
    public function update(Request $request, $id)
    {
        $package = TravelPackage::where('travel_agent_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'harga_per_orang' => 'required|numeric|min:0',
            'durasi_hari' => 'required|integer|min:1',
            'tanggal_keberangkatan' => 'required|date',
            'destinasi' => 'required|array|min:1',
            'fasilitas_include' => 'nullable|array',
            'fasilitas_exclude' => 'nullable|array',
            'itinerary' => 'nullable|array',
            'min_peserta' => 'required|integer|min:1',
            'max_peserta' => 'required|integer|min:1',
            'meeting_point' => 'required|string',
            'whatsapp' => 'nullable|string',
            'email' => 'nullable|email',
            'instagram' => 'nullable|string',
            'website' => 'nullable|url',
        ]);

        // Handle thumbnail
        if ($request->hasFile('thumbnail')) {
            if ($package->thumbnail) {
                \Storage::disk('public')->delete($package->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('travel-packages', 'public');
        }

        $package->update($validated);

        return redirect()->route('travel-agent.packages.index')
            ->with('success', 'Paket wisata berhasil diupdate!');
    }

    /**
     * Delete paket
     */
    public function destroy($id)
    {
        $package = TravelPackage::where('travel_agent_id', auth()->id())->findOrFail($id);
        
        if ($package->thumbnail) {
            \Storage::disk('public')->delete($package->thumbnail);
        }

        $package->delete();

        return redirect()->route('travel-agent.packages.index')
            ->with('success', 'Paket wisata berhasil dihapus!');
    }

    /**
     * View detail paket
     */
    public function show($id)
    {
        $package = TravelPackage::where('travel_agent_id', auth()->id())->findOrFail($id);
        return view('travel-agent.packages.show', compact('package'));
    }
}