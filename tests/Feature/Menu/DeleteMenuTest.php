<?php

namespace Tests\Feature\Menu;

use App\Models\Menu;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlateSeeder;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteMenuTest extends TestCase
{
    use RefreshDatabase;

    public function setUp (): void {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            RestaurantSeeder::class,
            PlateSeeder::class,
            MenuSeeder::class,
        ]);
    }

    protected $baseAPI = "api/v1";

    public function test_delete_menu(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;
        $menu = $restaurant->menus->first();

        $response = $this->apiAs($user, "delete", $this->baseAPI . "/" . $restaurant->slug . "/menu/" . $menu->slug);

        $response->assertStatus(200);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);

    }

    public function test_you_are_not_the_owner_of_this_restaurant(): void
    {
        $restaurant = Restaurant::first();
        $user = User::where("name", "!=", "mauro")->first();
        $menu = $restaurant->menus->first();

        $response = $this->apiAs($user, "delete", $this->baseAPI . "/" . $restaurant->slug . "/menu/" . $menu->slug);

        $response->assertStatus(403);
    }

    public function test_you_are_not_the_owner_of_this_menu(): void
    {
        $menu = Menu::first();
        $restaurant = Restaurant::whereNot("user_id", $menu->restaurant->user_id)->first();
        $user = $restaurant->user;

        $response = $this->apiAs($user, "delete", $this->baseAPI . "/" . $restaurant->slug . "/menu/" . $menu->slug);

        $response->assertStatus(403);
    }
}
