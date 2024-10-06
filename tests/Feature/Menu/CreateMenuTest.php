<?php

namespace Tests\Feature\Menu;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateMenuTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();

        $this->seed([RestaurantSeeder::class]);
    }

    protected $baseAPI = "/api/v1";

    public function test_create_my_menu(): void
    {
        $restaurant = Restaurant::first();
        $user = User::first();

        $data = [
            "name" => "Hola mundo",
            "description" =>"Sit voluptatum explicabo quaerat minima hic. Harum officiis illum fuga accusantium neque minima, obcaecati, voluptatum Repudiandae iste quisquam vel laborum pariatur. Officiis voluptas saepe ipsum asperiores nobis. Nulla consectetur fugit.",
            "restaurant_id" => $restaurant->id
        ];

        $response = $this->apiAs($user, "post", $this->baseAPI. "/" . $restaurant->slug .'/menu', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure(["data", "errors", "message"]);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonFragment(["data" =>
            ["menu" =>
                [
                    "name" => $data["name"],
                    "description" => $data["description"],
                    "restaurant" => $restaurant->name,
                ]
            ]
        ]);
        $response->assertJsonFragment(["errors" => null]);
        $this->assertDatabaseCount("menus", 1);
    }
}
