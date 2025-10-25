<?php

namespace Database\Seeders;

use App\Enums\NewsSource;

use App\Models\User;
use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@innoscripta.com',
        ]);

        UserPreference::create([
            'user_id' => $user->id,
            'sources' => [ 
                NewsSource::NEWS_CRED->value,
                NewsSource::THE_GUARDIAN->value,
                NewsSource::NEWS_DATA_IO->value,
            ],
            'categories' => ['technology', 'business'],
            'authors' => [],
        ]);

          // Create sample articles from different sources
        Article::factory()->count(20)->newsapi()->create();
        Article::factory()->count(15)->theguardian()->create();
        Article::factory()->count(10)->newsdataio()->create();
        
        // Create articles with specific categories
        Article::factory()->count(10)->technology()->create();
        Article::factory()->count(10)->business()->create();

        $this->command->info('Database seeded successfully!');
    }
}
