<?php

namespace Database\Seeders;

use App\Models\Meta;
use App\Models\Sheet;
use Illuminate\Database\Seeder;

class SheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sheet::factory()->count(3)->create()->each(function ($sheet) {
            $meta = Meta::factory()->make([
                'metaable_id' => $sheet->id,
                'metaable_type' => 'App\Models\Sheet',
            ]);

            return $sheet->meta()->create($meta->toArray());
        });

        Sheet::factory()->count(5)->inReview()->create()->each(function ($sheet) {
            $meta = Meta::factory()->make([
                'metaable_id' => $sheet->id,
                'metaable_type' => 'App\Models\Sheet',
            ]);

            return $sheet->meta()->create($meta->toArray());
        });

        Sheet::factory()->count(15)->published()->create()->each(function ($sheet) {
            $meta = Meta::factory()->make([
                'metaable_id' => $sheet->id,
                'metaable_type' => 'App\Models\Sheet',
            ]);

            return $sheet->meta()->create($meta->toArray());
        });
    }
}
