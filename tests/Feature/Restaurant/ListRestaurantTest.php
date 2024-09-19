<?php

namespace Tests\Feature\Restaurant;

use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListRestaurantTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);

        User::factory()->create(["name" => "luciano"]);
        Restaurant::factory(5)->create([
            "user_id" => 1 // es el usuario que se creo con los seeder
        ]);
    }

    protected $baseAPI = "api/v1";

    public function test_list_my_restaurants(): void
    {
        $user = User::where(["name" => "mauro"])->first();
        $restaurants = $user->restaurants;

        $response = $this->apiAs($user, "get", $this->baseAPI . '/restaurant/');

        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "errors", "data"]);
        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonFragment(["data" => [
                "restaurants" => RestaurantResource::collection($restaurants)->resolve()
            ]
        ]);
    }
}
