<?php

namespace Database\Factories;

use App\Models\Runway;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RunwayFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Runway::class;

    /**
     * Indicate that the page is in review status.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inReview()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Review',
            ];
        });
    }

    /**
     * Indicate that the page is in published status.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function published()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Published',
            ];
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->sentence(rand(1, 8));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'status' => 'Draft',
            'content' => [
                [
                    'full_width' => true,
                    'bg_color' => 'primary',
                    'blocks' => [
                        [
                            'type' => 'rich-text',
                            'data' => [
                                'content' => '<h1>'.Str::title($this->faker->words(rand(3, 8), true)).'</h1><p>'.collect($this->faker->paragraphs(rand(1, 6)))->implode('</p><p>').'</p><h2>'.Str::title($this->faker->words(rand(3, 8), true)).'</h2><p>'.collect($this->faker->paragraphs(rand(1, 6)))->implode('</p><p>').'</p>',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
