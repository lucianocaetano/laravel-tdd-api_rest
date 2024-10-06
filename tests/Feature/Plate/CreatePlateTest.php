<?php

namespace Tests\Feature\Plate;

use App\Models\Restaurant;
use Database\Seeders\RestaurantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreatePlateTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();

        $this->seed(RestaurantSeeder::class);

    }

    protected $baseAPI = "api/v1";

    public function test_create_plates(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;

        $data = [
            'name' => "Milanesas",
            'description' => "descripcion generica",
            'price' => 200,
            'image' => null
        ];

        $response = $this->apiAs($user, "post", $this->baseAPI . '/' . $restaurant->slug . '/plate', $data);

        $response->assertStatus(200);
        $this->assertDatabaseCount("plates", 1);
    }
}
