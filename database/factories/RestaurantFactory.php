<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Restaurant>
 */
class RestaurantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     **/


    public function definition(): array
    {
        $name = $this->faker->name;

        return [
            'name' => $name,
            'description' => $this->faker->text(200),
            'slug' => Str::slug($name),
            'user_id' =>  User::factory()
        ];
    }
}
