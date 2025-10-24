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
     * The path to your application's "home" route.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Default API rate limit
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Higher rate limit for authenticated users
        RateLimiter::for('api-authenticated', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(120)->by($request->user()->id)
                : Limit::perMinute(60)->by($request->ip());
        });

        // Strict rate limit for search endpoints
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many search requests. Please try again later.',
                    ], 429, $headers);
                });
        });

        // Rate limit for preference updates
        RateLimiter::for('preferences', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()->id);
        });
    }
}