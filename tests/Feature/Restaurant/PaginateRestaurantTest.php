<?php

namespace Tests\Feature\Restaurant;

use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaginateRestaurantTest extends TestCase
{
    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);

        Restaurant::factory(30)->create([
            "user_id" => User::first()->id
        ]);
    }

    protected $baseAPI = "api/v1";

    public function test_restaurant_pagination(): void
    {
        $user = User::where(["name" => "mauro"])->first();
        $restaurants = $user->restaurants()->paginate();

        $response = $this->apiAs($user, "get", $this->baseAPI . '/restaurant');

        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "errors", "data"]);
        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonFragment(["data" => [
                "restaurants" => RestaurantResource::collection($restaurants)->resolve(),
                "count" => 15,
                "current_page" => 1,
                "last_page" => 2,
                "per_page" => 15,
                "total" => 30
            ]
        ]);
        $response->assertJsonCount(15, "data.restaurants");
    }

    public function test_restaurant_page_2(): void
    {
        $user = User::where(["name" => "mauro"])->first();

        $page = 2;
        $restaurants = $user->restaurants()->paginate(15, ['*'], 'page', $page);

        $response = $this->apiAs($user, "get", $this->baseAPI . '/restaurant?page=2');

        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "errors", "data"]);
        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonFragment(["data" => [
                "restaurants" => RestaurantResource::collection($restaurants)->resolve(),
                "count" => 15,"current_page" => 2, "last_page" => 2, "per_page" => 15, "total" => 30
            ]
        ]);
        $response->assertJsonCount(15, "data.restaurants");
    }

}
