<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminTravelAgentController extends Controller
{
    /**
     * List semua travel agents
     */
    public function index()
    {
        $travelAgents = User::where('role', 'travel_agent')
            ->withCount('travelAgentPackages', 'travelAgentTransactions')
            ->latest()
            ->paginate(15);

        return view('admin.travel-agents.index', compact('travelAgents'));
    }

    /**
     * Form create travel agent
     */
    public function create()
    {
        return view('admin.travel-agents.create');
    }

    /**
     * Store travel agent baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_telepon' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_telepon' => $validated['no_telepon'],
            'password' => Hash::make($validated['password']),
            'role' => 'travel_agent',
        ]);

        // Auto create Basic subscription
        \App\Models\TravelAgentSubscription::create([
            'travel_agent_id' => $user->id,
            'package_id' => \App\Models\TravelAgentSubscriptionPackage::where('nama_paket', 'Basic')->first()->id,
            'payment_method' => 'free',
            'status' => 'active',
            'started_at' => now(),
            'expired_at' => null,
        ]);

        return redirect()->route('admin.travel-agents.index')
            ->with('success', 'Travel Agent berhasil dibuat!');
    }

    /**
     * Form edit travel agent
     */
    public function edit($id)
    {
        $travelAgent = User::where('role', 'travel_agent')->findOrFail($id);
        return view('admin.travel-agents.edit', compact('travelAgent'));
    }

    /**
     * Update travel agent
     */
    public function update(Request $request, $id)
    {
        $travelAgent = User::where('role', 'travel_agent')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'no_telepon' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $travelAgent->name = $validated['name'];
        $travelAgent->email = $validated['email'];
        $travelAgent->no_telepon = $validated['no_telepon'];

        if ($request->filled('password')) {
            $travelAgent->password = Hash::make($validated['password']);
        }

        $travelAgent->save();

        return redirect()->route('admin.travel-agents.index')
            ->with('success', 'Travel Agent berhasil diupdate!');
    }

    /**
     * Delete travel agent
     */
    public function destroy($id)
    {
        $travelAgent = User::where('role', 'travel_agent')->findOrFail($id);
        $travelAgent->delete();

        return redirect()->route('admin.travel-agents.index')
            ->with('success', 'Travel Agent berhasil dihapus!');
    }

    /**
     * Show detail travel agent
     */
    public function show($id)
    {
        $travelAgent = User::where('role', 'travel_agent')->findOrFail($id);
        
        $stats = [
            'total_packages' => \App\Models\TravelPackage::where('travel_agent_id', $id)->count(),
            'active_packages' => \App\Models\TravelPackage::where('travel_agent_id', $id)->where('status', 'active')->count(),
            'total_transactions' => \App\Models\TravelAgentSubscription::where('travel_agent_id', $id)->count(),
        ];

        $activeSubscription = \App\Models\TravelAgentSubscription::where('travel_agent_id', $id)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expired_at')
                      ->orWhere('expired_at', '>', now());
            })
            ->with('package')
            ->latest()
            ->first();

        $subscriptions = \App\Models\TravelAgentSubscription::where('travel_agent_id', $id)
            ->with('package')
            ->latest()
            ->paginate(10);

        return view('admin.travel-agents.show', compact('travelAgent', 'stats', 'subscriptions', 'activeSubscription'));
    }
}