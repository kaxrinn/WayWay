<?php

namespace App\Http\Controllers;

use App\Models\TravelAgentSubscriptionPackage;
use App\Models\TravelAgentSubscription;
use Illuminate\Http\Request;

class AdminTravelSubscriptionPackageController extends Controller
{
    /**
     * List semua paket yang ditawarkan ke travel agent
     */
    public function index()
    {
        $packages = TravelAgentSubscriptionPackage::withCount('subscriptions')->latest()->get();
        return view('admin.travel-subscriptions.packages.index', compact('packages'));
    }

    /**
     * Create paket baru
     */
    public function create()
    {
        return view('admin.travel-subscriptions.packages.create');
    }

    /**
     * Store paket baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'max_packages' => 'required|integer|min:1',
            'durasi_bulan' => 'required|integer|min:1',
            'fitur' => 'nullable|array',
        ]);

        TravelAgentSubscriptionPackage::create($validated);

        return redirect()->route('admin.travel-subscriptions.packages.index')
            ->with('success', 'Paket berhasil dibuat!');
    }

    /**
     * Edit paket
     */
    public function edit($id)
    {
        $package = TravelAgentSubscriptionPackage::findOrFail($id);
        return view('admin.travel-subscriptions.packages.edit', compact('package'));
    }

    /**
     * Update paket
     */
    public function update(Request $request, $id)
    {
        $package = TravelAgentSubscriptionPackage::findOrFail($id);

        $validated = $request->validate([
            'nama_paket' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'max_packages' => 'required|integer|min:1',
            'durasi_bulan' => 'required|integer|min:1',
            'fitur' => 'nullable|array',
        ]);

        $package->update($validated);

        return redirect()->route('admin.travel-subscriptions.packages.index')
            ->with('success', 'Paket berhasil diupdate!');
    }

    /**
     * Delete paket
     */
    public function destroy($id)
    {
        $package = TravelAgentSubscriptionPackage::findOrFail($id);
        $package->delete();

        return redirect()->route('admin.travel-subscriptions.packages.index')
            ->with('success', 'Paket berhasil dihapus!');
    }

    /**
     * Toggle status paket
     */
    public function toggleStatus($id)
    {
        $package = TravelAgentSubscriptionPackage::findOrFail($id);
        $package->update([
            'status' => $package->status === 'active' ? 'inactive' : 'active'
        ]);

        return back()->with('success', 'Status paket berhasil diubah!');
    }
}