<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\PaketPromosi;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        User::created(function ($user) {
            if ($user->role === 'pemilik_wisata') {
                $basicPaket = PaketPromosi::where('nama_paket', 'Basic')->first();
                
                $user->update([
                    'current_paket_id' => $basicPaket->id,
                    'paket_expired_at' => null,
                ]);
            }
        });
    }
}
