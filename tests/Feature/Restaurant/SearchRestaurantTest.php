<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class
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
            'name' => "one"
        ]);

        Restaurant::factory()->create([
            'user_id' => $this->user->id,
            'description' => "two"
        ]);

        Restaurant::factory()->create([
            'user_id' => $this->user->id,
            'description' => "two"
        ]);
    }

    public function test_search_restaurant(): void
    {

        $params = [
            'search' => 'two'
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

        $response->assertJsonCount(2, 'data.restaurants');

        $response->assertJsonPath('data.restaurants.0.description', 'two');
        $response->assertJsonPath('data.restaurants.1.description', 'two');
    }
}
