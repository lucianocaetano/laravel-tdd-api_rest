<?php

namespace Tests\Feature\Menu;

use App\Http\Resources\PlateResource;
use App\Models\Menu;
use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\PlateSeeder;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
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

        $this->seed([UserSeeder::class, RestaurantSeeder::class, PlateSeeder::class]);
    }

    protected $baseAPI = "/api/v1";

    protected $data = [
        "name" => "Hola mundo",
        "description" =>"Sit voluptatum explicabo quaerat minima hic. Harum officiis illum fuga accusantium neque minima, obcaecati, voluptatum Repudiandae iste quisquam vel laborum pariatur. Officiis voluptas saepe ipsum asperiores nobis. Nulla consectetur fugit.",
    ];

    public function test_create_my_menu(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;
        $plate = $restaurant->plates()->first();

        $response = $this->apiAs($user, "post", $this->baseAPI. "/" . $restaurant->slug .'/menu', [
            ...$this->data,
            "restaurant_id" => $restaurant->id,
            "plate_ids" => [$plate->id, $plate->id]
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonPath("data.menu.name", $this->data["name"]);
        $response->assertJsonPath("data.menu.description", $this->data["description"]);
        $response->assertJsonPath("data.menu.restaurant", $restaurant->name);
        $response->assertJsonPath("data.menu.plates", [PlateResource::make($plate)->resolve()]);

        $response->assertJsonFragment(["errors" => null]);
        $this->assertDatabaseCount("menus", 1);
        $this->assertDatabaseCount("plate_menu", 1);
    }

    public function test_you_are_not_the_owner_of_this_restaurant(): void
    {
        $restaurant = Restaurant::first();
        $user = User::where("name", "=", "mauro")->first();

        $response = $this->apiAs($user, "post", $this->baseAPI . "/" . $restaurant->slug . "/menu", [
            ...$this->data,
            "restaurant_id" => $restaurant->id,
            "plate_ids" => []
        ]);

        $response->assertStatus(403);
    }
}
