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

class EditMenuTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
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
        $response->assertJsonStructure(["data" => [
            "menu" => [
                "name",
                "slug",
                "description",
                "restaurant",
                'links' => [
                    'self',
                    'index',
                    'store',
                    'update',
                    'delete',
                ],
            ]
        ]]);
        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonPath('data.menu.links.self', route('menu.show', ['restaurant' => $restaurant->slug, 'menu' => $menu->id]));
        $response->assertJsonPath('data.menu.links.index', route('menu.index', ['restaurant' => $restaurant->slug]));
        $response->assertJsonPath('data.menu.links.store', route('menu.store', ['restaurant' => $restaurant->slug]));
        $response->assertJsonPath('data.menu.links.update', route('menu.update', ['restaurant' => $restaurant->slug, 'menu' => $menu->id]));
        $response->assertJsonPath('data.menu.links.delete', route('menu.destroy', ['restaurant' => $restaurant->slug, 'menu' => $menu->id]));
    }

    public function test_you_are_not_the_owner_of_this_restaurant(): void
    {
        $user = User::first();
        $restaurant = Restaurant::whereNot('user_id', $user->id)->first();
        $menu = $restaurant->menus->first();

        $response = $this->apiAs($user, "patch", $this->baseAPI . "/" . $restaurant->slug . "/menu/" . $menu->slug, $this->data);

        $response->assertStatus(403);
    }

    public function test_you_are_not_the_owner_of_this_menu(): void
    {
        $menu = Menu::first();
        $restaurant = Restaurant::whereNot("user_id", $menu->restaurant->user_id)->first();
        $user = $restaurant->user;

        $response = $this->apiAs($user, "patch", $this->baseAPI . "/" . $restaurant->slug . "/menu/" . $menu->slug, $this->data);

        $response->assertStatus(403);
    }
}
