<?php

namespace App\Http\Controllers;

use App\Models\TravelPackage;
use App\Models\TravelAgentSubscription;
use App\Models\TravelAgentSubscriptionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TravelAgentController extends Controller
{

    public function dashboard()
    {
        $userId = auth()->id();

        $activeSubscription = TravelAgentSubscription::where('travel_agent_id', $userId)
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expired_at')
                  ->orWhere('expired_at', '>', now());
            })
            ->with('package')
            ->latest('started_at')
            ->first();

        if (!$activeSubscription) {
            $basicPackage = TravelAgentSubscriptionPackage::where('nama_paket', 'Basic')->first();
            
            if ($basicPackage) {
                $activeSubscription = TravelAgentSubscription::where('travel_agent_id', $userId)
                    ->where('package_id', $basicPackage->id)
                    ->with('package')
                    ->first();
                
                if (!$activeSubscription) {
                    $activeSubscription = TravelAgentSubscription::create([
                        'travel_agent_id' => $userId,
                        'package_id' => $basicPackage->id,
                        'payment_method' => 'free',
                        'status' => 'active',
                        'started_at' => now(),
                        'expired_at' => null,
                    ]);
                    $activeSubscription->load('package');
                }
            }
        }

        $stats = [
            'total_packages' => TravelPackage::where('travel_agent_id', $userId)->count(),
            'active_packages' => TravelPackage::where('travel_agent_id', $userId)
                ->where('status', 'active')
                ->count(),
        ];

        $maxPackages = $activeSubscription?->package->max_packages ?? 1;
        $currentPackages = $stats['total_packages'];
        $packagesAvailable = max(0, $maxPackages - $currentPackages);

        \Log::info('Travel Agent Dashboard', [
            'user_id' => $userId,
            'subscription' => $activeSubscription?->id,
            'package' => $activeSubscription?->package->nama_paket ?? 'NONE',
            'max_packages' => $maxPackages,
            'current_packages' => $currentPackages,
            'available' => $packagesAvailable,
        ]);

        return view('travel-agent.dashboard', [
            'stats' => $stats,
            'activeSubscription' => $activeSubscription,
            'maxPackages' => $maxPackages,
            'currentPackages' => $currentPackages,
            'packagesAvailable' => $packagesAvailable,
        ]);
    }

    // update  profile
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'no_telepon'=> 'nullable|string|max:20',
            'password'  => 'nullable|min:8|confirmed',
        ]);

        $user->name       = $validated['name'];
        $user->email      = $validated['email'];
        $user->no_telepon = $validated['no_telepon'] ?? $user->no_telepon;

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}