<?php

namespace Tests\Feature\Menu;

use App\Http\Resources\PlateResource;
use App\Models\Menu;
use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\PlateSeeder;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateMenuTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();

        $this->seed([UserSeeder::class, RestaurantSeeder::class, PlateSeeder::class]);
    }

    protected $baseAPI = "/api/v1";

    protected $data = [
        "name" => "Hola mundo",
        "description" => "Sit voluptatum explicabo quaerat minima hic. Harum officiis illum fuga accusantium neque minima, obcaecati, voluptatum Repudiandae iste quisquam vel laborum pariatur. Officiis voluptas saepe ipsum asperiores nobis. Nulla consectetur fugit.",
    ];

    public function test_create_my_menu(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;
        $plate = $restaurant->plates()->first();

        $response = $this->apiAs($user, "post", $this->baseAPI . "/" . $restaurant->slug . '/menu', [
            ...$this->data,
            "restaurant_id" => $restaurant->id,
            "plate_ids" => [$plate->id, $plate->id]
        ]);

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

        $menu = Menu::find(1);

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
        $restaurant = Restaurant::first();
        $user = User::where("name", "!=", "mauro")->first();

        $response = $this->apiAs($user, "post", $this->baseAPI . "/" . $restaurant->slug . "/menu", [
            ...$this->data,
            "restaurant_id" => $restaurant->id,
            "plate_ids" => []
        ]);

        $response->assertStatus(403);
    }
}
