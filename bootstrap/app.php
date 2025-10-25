<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // 
        $schedule->command('news:fetch')
            ->hourly()
            ->withoutOverlapping()
            ->onSuccess(function () {
                //runInBackground()
                \Log::channel('news_aggregator')->info('News fetch completed successfully');
            })
            ->onFailure(function () {
                \Log::channel('news_aggregator')->error('News fetch failed');
            });
    })
    ->create();
