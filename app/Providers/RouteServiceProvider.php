<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });

        $this->configureModelBindings();
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Configure model bindings
     */
    protected function configureModelBindings(): void
    {
        Route::model('dossier', \App\Models\Dossier::class);
        Route::model('contract', \App\Models\Contract::class);
        Route::model('contractTemplate', \App\Models\ContractTemplate::class);
        Route::model('contractType', \App\Models\ContractType::class);
        Route::model('litigant', \App\Models\Litigant::class);
        Route::model('asset', \App\Models\Asset::class);

        // Custom binding với constraints
        Route::bind('dossier', function ($value) {
            return \App\Models\Dossier::where('id', $value)
                ->where('created_by', auth()->id())
                ->firstOrFail();
        });

        Route::bind('contract', function ($value, $route) {
            $contract = \App\Models\Contract::findOrFail($value);

            // Kiểm tra contract thuộc về dossier trong route
            if ($route->hasParameter('dossier')) {
                $dossier = $route->parameter('dossier');
                if ($contract->dossier_id !== $dossier->id) {
                    abort(404, 'Contract không thuộc về dossier này');
                }
            }

            return $contract;
        });
    }
}
