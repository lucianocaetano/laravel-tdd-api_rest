<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Menu>
 */
class MenuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name();

        return [
            "name" => $name,
            "slug" => str($name. " " . uniqid())->slug()->value(),
            "description" =>  substr($this->faker->paragraph(), 0, 255),
        ];
    }
}
