<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plate>
 */
class PlateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $name = fake()->words(2, true);
        return [
            'price' => fake()->numberBetween(100, 1000),
            'name' => $name,
            'description' => fake()->text,
            'slug' => Str::slug($name . ' ' . uniqid()),
        ];
    }
}
