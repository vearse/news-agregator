<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\NewsSourceInterface;
use App\Services\NewsAggregator\Sources\NewsDataIoSource;
use App\Services\NewsAggregator\Sources\NewsAPISource;
use App\Services\NewsAggregator\Sources\NewYorkTimesSource;
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

        $this->app->bind('news.newsdataio', function ($app) {
            return new NewsDataIoSource();
        });

        $this->app->bind('news.newyourktimes', function ($app) {
            return new NewYorkTimesSource();
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
