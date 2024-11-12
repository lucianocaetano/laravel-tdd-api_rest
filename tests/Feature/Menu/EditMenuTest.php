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

class EditMenuTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class,
            RestaurantSeeder::class,
            PlateSeeder::class,
            MenuSeeder::class,
        ]);
    }

    protected $baseAPI = "api/v1";
    public $data = [
        "name" => "Menu name"
    ];

    public function test_edit_menu(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;
        $menu = $restaurant->menus->first();


        $response = $this->apiAs($user, "patch", $this->baseAPI . "/" . $restaurant->slug . "/menu/" . $menu->slug, $this->data);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "message", "errors", "data" => [
                "menu" => ["name", "description", "slug", "restaurant", "plates"]
            ]
        ]);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);

        $response->assertJsonPath("data.menu.name", "Menu name");
    }

    public function test_you_are_not_the_owner_of_this_restaurant(): void
    {
        $restaurant = Restaurant::first();
        $user = User::where("name", "=", "mauro")->first();
        $menu = $restaurant->menus->first();

        $response = $this->apiAs($user, "patch", $this->baseAPI . "/" . $restaurant->slug . "/menu/" . $menu->slug, $this->data);

        $response->assertStatus(403);
    }

    public function test_you_are_not_the_owner_of_this_menu(): void
    {
        $restaurant = Restaurant::first();
        $user = User::where("name", "=", "mauro")->first();
        $menu = Menu::whereNot("restaurant_id", $restaurant->id)->first();

        $response = $this->apiAs($user, "patch", $this->baseAPI . "/" . $restaurant->slug . "/menu/" . $menu->slug, $this->data);

        $response->assertStatus(403);
    }
}
