<?php

namespace App\Providers;

use App\Repositories\Contracts\MeasurementRepositoryInterface;
use App\Repositories\Contracts\RankingRepositoryInterface;
use App\Repositories\Eloquent\MeasurementRepository;
use App\Repositories\Eloquent\RankingRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MeasurementRepositoryInterface::class, MeasurementRepository::class);
        $this->app->bind(RankingRepositoryInterface::class, RankingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
