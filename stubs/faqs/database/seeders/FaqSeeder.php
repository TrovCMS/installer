<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Meta;
use Illuminate\Database\Seeder;
use Spatie\Tags\Tag;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Faq::factory()
            ->count(3)
            ->create()->each(function ($faq) {
                $meta = Meta::factory()->make([
                    'metaable_id' => $faq->id,
                    'metaable_type' => 'App\Models\Faq',
                ]);

                return $faq->meta()->create($meta->toArray());
            });

        Faq::factory()
            ->count(5)
            ->inReview()
            ->create()->each(function ($faq) {
                $meta = Meta::factory()->make([
                    'metaable_id' => $faq->id,
                    'metaable_type' => 'App\Models\Faq',
                ]);

                $faq->attachTag(Tag::factory()->create(['type' => 'faq']));

                return $faq->meta()->create($meta->toArray());
            });

        Faq::factory()
            ->count(15)
            ->published()
            ->create()->each(function ($faq) {
                $meta = Meta::factory()->make([
                    'metaable_id' => $faq->id,
                    'metaable_type' => 'App\Models\Faq',
                ]);

                $faq->attachTag(Tag::factory()->create(['type' => 'faq']));

                return $faq->meta()->create($meta->toArray());
            });
    }
}
