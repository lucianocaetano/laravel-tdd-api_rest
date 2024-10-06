<?php

namespace Tests\Feature\Plate;

use App\Models\Plate;
use Database\Seeders\PlateSeeder;
use Database\Seeders\RestaurantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditPlateTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();

        $this->seed([RestaurantSeeder::class, PlateSeeder::class]);

    }

    protected $baseAPI = "api/v1";

    public function test_edit_my_plate(): void
    {
        $plate = Plate::first();
        $restaurant = $plate->restaurant;
        $user = $restaurant->user;

        $data = [
            'name' => "Milanesas",
        ];

        $response = $this->apiAs($user, "put", $this->baseAPI . "/" . $restaurant->slug . '/plate/' . $plate->slug, $data);

        $response->assertStatus(200);
        $this->assertTrue($plate->name !== $response->json()["data"]["restaurant"]["name"]);
        $response->assertJsonStructure(["message", "errors", "data"]);
        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonFragment(["data" => [
            "restaurant" => [
                "description" => $plate->description,
                "image" => null,
                "name" => "Milanesas",
                "price" => $plate->price,
                "restaurant_id" => $plate->restaurant_id
            ]
        ]]);
    }

}
