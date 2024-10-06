<?php

namespace Tests\Feature\Restaurant;

use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowRestaurantTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();
        $this->seed([UserSeeder::class, RestaurantSeeder::class]);

        Restaurant::factory()->create([
            "user_id" => User::first()->id
        ]);
    }

    protected $baseAPI = "api/v1";

    public function test_show_my_restaurant(): void
    {
        $user = User::where(["name" => "mauro"])->first();
        $restaurant = $user->restaurants->first();

        $response = $this->apiAs($user, "get", $this->baseAPI . '/restaurant/' . $restaurant->slug);

        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "errors", "data"]);
        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonFragment(["data" => [
                "restaurant" => RestaurantResource::make($restaurant)->resolve()
            ]
        ]);
    }

    public function test_show_a_not_my_restaurant(): void
    {
        $user = User::where(["name" => "mauro"])->first();
        $restaurant = Restaurant::first();

        $response = $this->apiAs($user, "get", $this->baseAPI . '/restaurant/' . $restaurant->slug);

        $response->assertStatus(403);
    }

}
