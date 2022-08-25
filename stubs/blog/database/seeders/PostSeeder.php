<?php

namespace Database\Seeders;

use App\Models\Meta;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Spatie\Tags\Tag;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::factory()->count(3)->create()->each(function ($post) {
            $meta = Meta::factory()->make([
                'metaable_id' => $post->id,
                'metaable_type' => 'App\Models\Post',
            ]);

            return $post->meta()->create($meta->toArray());
        });

        Post::factory()->count(5)->inReview()->create()->each(function ($post) {
            $meta = Meta::factory()->make([
                'metaable_id' => $post->id,
                'metaable_type' => 'App\Models\Post',
            ]);

            $post->attachTag(Tag::factory()->create(['type' => 'post']));

            return $post->meta()->create($meta->toArray());
        });

        Post::factory()->count(15)->published()->create()->each(function ($post) {
            $meta = Meta::factory()->make([
                'metaable_id' => $post->id,
                'metaable_type' => 'App\Models\Post',
            ]);

            $post->attachTag(Tag::factory()->create(['type' => 'post']));

            return $post->meta()->create($meta->toArray());
        });
    }
}
