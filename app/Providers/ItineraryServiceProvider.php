<?php

namespace App\Providers;

use App\Services\ContentFilterService;
use App\Services\BayesianScoringService;
use App\Services\HaversineService;
use App\Services\GreedyRouterService;
use App\Services\OsrmService;
use App\Services\ItineraryService;
use Illuminate\Support\ServiceProvider;

class ItineraryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Singleton supaya instance tidak dibuat berulang-ulang
        $this->app->singleton(HaversineService::class);
        $this->app->singleton(ContentFilterService::class);
        $this->app->singleton(BayesianScoringService::class);
        $this->app->singleton(OsrmService::class);

        $this->app->singleton(GreedyRouterService::class, function ($app) {
            return new GreedyRouterService(
                $app->make(HaversineService::class)
            );
        });

        $this->app->singleton(ItineraryService::class, function ($app) {
            return new ItineraryService(
                $app->make(ContentFilterService::class),
                $app->make(BayesianScoringService::class),
                $app->make(HaversineService::class),
                $app->make(GreedyRouterService::class),
                $app->make(OsrmService::class),
            );
        });
    }

    public function boot(): void
    {
      
    }
}
