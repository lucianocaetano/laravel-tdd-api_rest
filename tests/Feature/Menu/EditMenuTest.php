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

    public function setUp (): void {
        parent::setUp();

        $this->seed([
            UserSeeder::class,
            RestaurantSeeder::class,
            PlateSeeder::class,
            MenuSeeder::class,
        ]);
    }

    protected $baseAPI = "api/v1";

    public function test_edit_menu(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;
        $menu = $restaurant->menu;

        $data = [
           "name" => "Menu name"
        ];

        $response = $this->apiAs($user, "patch", $this->baseAPI . "/" . $restaurant->slug . "/menu/" . $menu->slug, $data);

        $response->assertStatus(200);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonFragment(["data" => [
            "menu" => [
                "name" => "Menu name",
                "description" => $menu->description,
                "restaurant" => $menu->restaurant->name,
                "plates" => $menu->plates->toArray()
            ]
        ]]);
    }

    public function test_menu_not_found_in_this_restaurant(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;
        $menu = Menu::where("restaurant_id", "!=", $restaurant->id)->first();

        $response = $this->apiAs($user, "patch", $this->baseAPI . "/" . $restaurant->slug . "/menu/" . $menu->slug);

        $response->assertStatus(404);
    }



}
