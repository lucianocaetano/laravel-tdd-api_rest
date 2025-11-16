<?php

namespace Tests\Feature\Menu;

use App\Models\Menu;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchMenuTest extends TestCase
{
    use RefreshDatabase;

    protected $user, $restaurant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $this->user = User::factory()->create(
            [
                "name" => "mauro",
                "email" => "lucianocaetano@gmail.com",
                "password" => "password123"
            ]
        );


        $this->restaurant = Restaurant::factory()->create([
            "user_id" => $this->user->id
        ]);

        Menu::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => "one"
        ]);

        Menu::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'description' => "two"
        ]);

        Menu::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'description' => "two"
        ]);
    }

    public function test_search_menus(): void
    {

        $params = [
            'search' => 'two'
        ];

        $response = $this->apiAs($this->user, 'get', route('menu.index', ['restaurant' => $this->restaurant->slug]), $params);

        $response->assertStatus(200);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);

        $response->assertJsonStructure([
            'data' => [
                'menus' => [
                    '*' => [
                        "name",
                        "slug",
                        "description",
                        "restaurant",
                        "links" => [
                            "self",
                            "index",
                            "store",
                            "update",
                            "delete"
                        ],
                    ]
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ],
            'message',
            'errors',
        ]);

        $response->assertJsonCount(2, 'data.menus');

        $response->assertJsonPath('data.menus.0.description', 'two');
        $response->assertJsonPath('data.menus.1.description', 'two');
    }
}
