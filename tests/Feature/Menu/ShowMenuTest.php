<?php

namespace Tests\Feature\Menu;

use App\Models\Menu;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PlateSeeder;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowMenuTest extends TestCase
{
    use RefreshDatabase;

    public function setUp (): void {
        parent::setUp();

        $this->seed([
            RestaurantSeeder::class,
            UserSeeder::class,
            PlateSeeder::class,
            MenuSeeder::class,
        ]);
    }

    protected $baseAPI = "api/v1";

    public function test_show_menu(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;
        $menu = $restaurant->menus->first();

        $response = $this->apiAs($user, "get", $this->baseAPI . "/" . $restaurant->slug . "/menu/" . $menu->slug);

        $response->assertStatus(200);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonStructure(["data" => [
            "menu" => [
                "name",
                "slug",
                "description",
                "restaurant",
                "plates",
            ]
        ]]);
    }

    public function test_you_are_not_the_owner_of_this_restaurant(): void
    {
        $restaurant = Restaurant::first();
        $user = User::where("name", "=", "mauro")->first();
        $menu = $restaurant->menus->first();

        $response = $this->apiAs($user, "get", $this->baseAPI . "/" . $restaurant->slug . "/menu/" . $menu->slug);

        $response->assertStatus(403);
    }

    public function test_you_are_not_the_owner_of_this_menu(): void
    {
        $restaurant = Restaurant::first();
        $user = User::where("name", "=", "mauro")->first();
        $menu = Menu::whereNot("restaurant_id", $restaurant->id)->first();

        $response = $this->apiAs($user, "get", $this->baseAPI . "/" . $restaurant->slug . "/menu/" . $menu->slug);

        $response->assertStatus(403);
    }
}
