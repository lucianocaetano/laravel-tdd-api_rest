<?php

namespace Tests\Feature\Menu;

use App\Http\Resources\MenuResource;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PlateSeeder;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListMenuTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RestaurantSeeder::class,
            MenuSeeder::class,
            UserSeeder::class,
            PlateSeeder::class,
        ]);
    }

    protected $baseAPI = "api/v1";

    public function test_list_menus_paginated(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;

        $response = $this->apiAs($user, "get", $this->baseAPI . "/" . $restaurant->slug . "/menu");

        $response->assertStatus(200);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);

        $response->assertJsonStructure([
            'data' => [
                "menus" => [
                    "*" => [
                        'name',
                        'slug',
                        'description',
                        'restaurant',
                        'plates' => [
                            '*' => [
                                'id',
                                'name',
                                "description",
                                "price",
                                "image",
                                "restaurant_id"
                            ]
                        ]
                    ],
                ],
                "total",
                "count",
                "per_page",
                "current_page",
                "last_page",
            ]
        ]);
        $response->assertJsonPath("data.total", 15);
        $response->assertJsonPath("data.count", 15);
        $response->assertJsonPath("data.per_page", 15);
        $response->assertJsonPath("data.current_page", 1);
        $response->assertJsonPath("data.last_page", 1);
    }

    public function test_list_menus_paginated_2(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;

        $response = $this->apiAs($user, "get", $this->baseAPI . "/" . $restaurant->slug . "/menu?page=2");

        $response->assertStatus(200);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);


        $response->assertJsonStructure([
            'data' => [
                "menus" => [
                    "*" => [
                        'name',
                        'slug',
                        'description',
                        'restaurant',
                        'plates' => [
                            '*' => [
                                'id',
                                'name',
                                "description",
                                "price",
                                "image",
                                "restaurant_id"
                            ]
                        ]
                    ],
                ],
                "total",
                "count",
                "per_page",
                "current_page",
                "last_page",
            ]
        ]);

        $response->assertJsonPath("data.total", 15);
        $response->assertJsonPath("data.count", 0);
        $response->assertJsonPath("data.per_page", 15);
        $response->assertJsonPath("data.current_page", 2);
        $response->assertJsonPath("data.last_page", 1);
    }

    public function test_you_are_not_the_owner_of_this_restaurant(): void
    {
        $restaurant = Restaurant::first();
        $user = User::where("name", "=", "mauro")->first();

        $response = $this->apiAs($user, "get", $this->baseAPI . "/" . $restaurant->slug . "/menu");

        $response->assertStatus(403);
    }
}
