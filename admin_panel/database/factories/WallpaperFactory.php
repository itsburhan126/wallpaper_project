<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class WallpaperFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'image' => 'https://picsum.photos/1080/1920?random=' . $this->faker->unique()->numberBetween(1, 1000),
            'thumbnail' => 'https://picsum.photos/300/500?random=' . $this->faker->unique()->numberBetween(1, 1000),
            'tags' => implode(',', $this->faker->words(3)),
            'downloads' => $this->faker->numberBetween(0, 1000),
            'views' => $this->faker->numberBetween(0, 5000),
            'status' => true,
        ];
    }
}
