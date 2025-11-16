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

class SortMenuTest extends TestCase
{
    use RefreshDatabase;

    protected $user, $restaurant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class
        ]);

        $this->user = User::factory()->create([
             "name" => "mauro",
             "email" => "lucianocaetano@gmail.com",
             "password" => "password123"
        ]);

        $this->restaurant = Restaurant::factory()->create([
            'user_id' => $this->user->id,
            'name' => "test",
            'description' => "test description",
        ]);

        Menu::factory()->create([
            "restaurant_id" => $this->restaurant->id,
            'name' => "A one",
            'description' => "A one",
        ]);

        Menu::factory()->create([
            "restaurant_id" => $this->restaurant->id,
            'name' => "B two",
            'description' => "B two",
        ]);

        Menu::factory()->create([
            "restaurant_id" => $this->restaurant->id,
            'name' => "C tree",
            'description' => "C tree"
        ]);
    }

    public function test_sort_by_name_menu(): void
    {
        $params = [
            'sort' => 'name'
        ];

        $response = $this->apiAs($this->user, 'get', route('menu.index', $this->restaurant->slug), $params);

        $response->assertStatus(200);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);

        $response->assertJsonStructure([
            'data' => [
                'menus' => [
                    '*' => [
                        'name',
                        'description',
                        'slug',
                        'restaurant',
                        'links' => [
                            'self',
                            'index',
                            'store',
                            'update',
                            'delete',
                        ],
                    ],
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

        $response->assertJsonPath('data.menus.0.name', 'A one');
        $response->assertJsonPath('data.menus.1.name', 'B two');
        $response->assertJsonPath('data.menus.2.name', 'C tree');
    }

    public function test_sort_by_description_menu(): void
    {
        $params = [
            'sort' => 'description'
        ];

        $response = $this->apiAs($this->user, 'get', route('menu.index', $this->restaurant->slug), $params);

        $response->assertStatus(200);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);

        $response->assertJsonStructure([
            'data' => [
                'menus' => [
                    '*' => [
                        'name',
                        'description',
                        'slug',
                        'restaurant',
                        'links' => [
                            'self',
                            'index',
                            'store',
                            'update',
                            'delete',
                        ],
                    ],
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

        $response->assertJsonPath('data.menus.0.description', 'A one');
        $response->assertJsonPath('data.menus.1.description', 'B two');
        $response->assertJsonPath('data.menus.2.description', 'C tree');
    }
}
