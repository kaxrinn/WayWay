<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\PaketPromosi;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 🔹 Default paket untuk pemilik wisata
        User::created(function ($user) {
            if ($user->role === 'pemilik_wisata') {
                $basicPaket = PaketPromosi::where('nama_paket', 'Basic')->first();

                if ($basicPaket) {
                    $user->update([
                        'current_paket_id' => $basicPaket->id,
                        'paket_expired_at' => null,
                    ]);
                }
            }
        });

        // 🔹 Pagination pakai Bootstrap
        Paginator::useBootstrapFive();
    }
}
