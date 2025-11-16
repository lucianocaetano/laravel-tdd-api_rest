<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SortRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

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

        Restaurant::factory()->create([
            'user_id' => $this->user->id,
            'name' => "A one",
            'description' => "A one",
        ]);

        Restaurant::factory()->create([
            'user_id' => $this->user->id,
            'name' => "B two",
            'description' => "B two",
        ]);

        Restaurant::factory()->create([
            'user_id' => $this->user->id,
            'name' => "C tree",
            'description' => "C tree"
        ]);
    }

    public function test_sort_by_name_restaurant(): void
    {
        $params = [
            'sort' => 'name'
        ];

        $response = $this->apiAs($this->user, 'get', route('restaurant.index'), $params);

        $response->assertStatus(200);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);

        $response->assertJsonStructure([
            'data' => [
                'restaurants' => [
                    '*' => [
                        'name',
                        'description',
                        'slug',
                        'user',
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

        $response->assertJsonPath('data.restaurants.0.name', 'A one');
        $response->assertJsonPath('data.restaurants.1.name', 'B two');
        $response->assertJsonPath('data.restaurants.2.name', 'C tree');
    }

    public function test_sort_by_description_restaurant(): void
    {
        $params = [
            'sort' => 'description'
        ];

        $response = $this->apiAs($this->user, 'get', route('restaurant.index'), $params);

        $response->assertStatus(200);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);

        $response->assertJsonStructure([
            'data' => [
                'restaurants' => [
                    '*' => [
                        'name',
                        'description',
                        'slug',
                        'user',
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

        $response->assertJsonPath('data.restaurants.0.description', 'A one');
        $response->assertJsonPath('data.restaurants.1.description', 'B two');
        $response->assertJsonPath('data.restaurants.2.description', 'C tree');
    }
}
