<?php

namespace Database\Seeders;

use App\Models\DiscoveryTopic;
use App\Models\Meta;
use Illuminate\Database\Seeder;

class DiscoveryTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DiscoveryTopic::factory()->count(3)->create()->each(function ($page) {
            $meta = Meta::factory()->make([
                'metaable_id' => $page->id,
                'metaable_type' => 'App\Models\DiscoveryTopic',
            ]);

            return $page->meta()->create($meta->toArray());
        });

        DiscoveryTopic::factory()->count(5)->inReview()->create()->each(function ($page) {
            $meta = Meta::factory()->make([
                'metaable_id' => $page->id,
                'metaable_type' => 'App\Models\DiscoveryTopic',
            ]);

            return $page->meta()->create($meta->toArray());
        });

        DiscoveryTopic::factory()->count(15)->published()->create()->each(function ($page) {
            $meta = Meta::factory()->make([
                'metaable_id' => $page->id,
                'metaable_type' => 'App\Models\DiscoveryTopic',
            ]);

            return $page->meta()->create($meta->toArray());
        });
    }
}
