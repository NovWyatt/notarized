<?php
namespace App\Providers;

use App\Services\LocationService;
use App\Services\SessionService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SessionService::class, function ($app) {
            return new SessionService();
        });
        $this->app->singleton(LocationService::class, function ($app) {
            return new LocationService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
