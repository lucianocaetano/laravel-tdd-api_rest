<?php

namespace Tests\Feature\Plate;

use App\Http\Resources\PlateResource;
use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\PlateSeeder;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowPlateTest extends TestCase
{
    use RefreshDatabase;

    function setUp(): void {
        parent::setUp();

        $this->seed([RestaurantSeeder::class, PlateSeeder::class, UserSeeder::class]);
    }

    public $baseAPI = "/api/v1";

    public function test_show_my_plate(): void
    {
        $plate = Plate::first();
        $restaurant = $plate->restaurant;
        $user = $restaurant->user;

        $response = $this->apiAs($user, "get", $this->baseAPI . "/" . $restaurant->slug . '/plate/' . $plate->slug);

        $response->assertStatus(200);
        $response->assertJsonStructure(["errors", "message", "data"]);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["data" =>
            [
            "plate" => PlateResource::make($plate)->resolve()
            ]
        ]);
    }

    public function test_you_are_not_the_owner_of_this_restaurant(): void
    {
        $plate = Plate::first();
        $restaurant = $plate->restaurant;
        $user = User::where("name", "=", "mauro")->first();

        $response = $this->apiAs($user, "delete", $this->baseAPI . "/" . $restaurant->slug . '/plate/' . $plate->slug);

        $response->assertStatus(403);
    }

    public function test_you_are_not_the_owner_of_this_plate(): void
    {
        $restaurant = Restaurant::first();
        $user = User::where("name", "=", "mauro")->first();
        $plate = Plate::whereNot("restaurant_id", $restaurant->id)->first();

        $response = $this->apiAs($user, "patch", $this->baseAPI . "/" . $restaurant->slug . "/plate/" . $plate->slug);

        $response->assertStatus(403);
    }
}
