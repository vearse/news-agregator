<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsAggregatorService;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch {--source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news from external APIs and store them in the database';
    public function __construct(
        protected NewsAggregatorService $aggregatorService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting news aggregation...');

        try {
            if ($source = $this->option('source')) {
                $this->info("Fetching from {$source}...");
                $result = $this->aggregatorService->aggregateFromSource($source);
                $this->displayResult($source, $result);
            } else {
                $this->info('Fetching from all sources...');
                $results = $this->aggregatorService->aggregateNews();
                
                foreach ($results as $source => $result) {
                    $this->displayResult($source, $result);
                }
            }

            $this->info('News aggregation completed successfully!');
            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    protected function displayResult(string $source, array $result): void
    {
        $this->line("  {$source}: Fetched {$result['fetched']}, Stored {$result['stored']}");
    }
}

// <?php

// namespace App\Console;

// use Illuminate\Console\Scheduling\Schedule;
// use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

// class Kernel extends ConsoleKernel
// {
//     /**
//      * Define the application's command schedule.
//      */
//     protected function schedule(Schedule $schedule): void
//     {
//         // Fetch news every hour
//         $schedule->command('news:fetch')
//             ->hourly()
//             ->withoutOverlapping()
//             ->runInBackground()
//             ->onSuccess(function () {
//                 \Log::info('News fetch completed successfully');
//             })
//             ->onFailure(function () {
//                 \Log::error('News fetch failed');
//             });

//         // Cleanup old articles daily at 2 AM
//         $schedule->command('articles:cleanup --days=30')
//             ->dailyAt('02:00')
//             ->onSuccess(function () {
//                 \Log::info('Articles cleanup completed');
//             });
//     }

//     /**
//      * Register the commands for the application.
//      */
//     protected function commands(): void
//     {
//         $this->load(__DIR__.'/Commands');

//         require base_path('routes/console.php');
//     }
// }