<?php

namespace Database\Seeders;

use App\Models\Runway;
use Illuminate\Database\Seeder;

class RunwaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Runway::factory()->count(3)->create();
        Runway::factory()->count(5)->inReview()->create();
        Runway::factory()->count(15)->published()->create();
    }
}
