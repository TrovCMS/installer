<?php

namespace Database\Seeders;

use App\Models\DiscoveryArticle;
use App\Models\Meta;
use Illuminate\Database\Seeder;

class DiscoveryArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DiscoveryArticle::factory()->count(3)->create()->each(function ($page) {
            $meta = Meta::factory()->make([
                'metaable_id' => $page->id,
                'metaable_type' => 'App\Models\DiscoveryArticle',
            ]);

            return $page->meta()->create($meta->toArray());
        });

        DiscoveryArticle::factory()->count(5)->inReview()->create()->each(function ($page) {
            $meta = Meta::factory()->make([
                'metaable_id' => $page->id,
                'metaable_type' => 'App\Models\DiscoveryArticle',
            ]);

            return $page->meta()->create($meta->toArray());
        });

        DiscoveryArticle::factory()->count(15)->published()->create()->each(function ($page) {
            $meta = Meta::factory()->make([
                'metaable_id' => $page->id,
                'metaable_type' => 'App\Models\DiscoveryArticle',
            ]);

            return $page->meta()->create($meta->toArray());
        });
    }
}
