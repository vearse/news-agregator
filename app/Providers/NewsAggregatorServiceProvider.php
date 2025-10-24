<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\NewsSourceInterface;
use App\Services\NewsAggregator\Sources\BBCNewsSource;
use App\Services\NewsAggregator\Sources\NewsAPISource;
use App\Services\NewsAggregator\Sources\NewsCredSource;
use App\Services\NewsAggregator\Sources\TheGuardianSource;

class NewsAggregatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind('news.newsapi', function ($app) {
            return new NewsAPISource();
        });

        $this->app->bind('news.theguardian', function ($app) {
            return new TheGuardianSource();
        });

        $this->app->bind('news.bbc', function ($app) {
            return new BBCNewsSource();
        });

        $this->app->bind('news.newscred', function ($app) {
            return new NewsCredSource();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
