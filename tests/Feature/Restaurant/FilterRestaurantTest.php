<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FilterRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

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
            'name' => "C two",
            'description' => "B two",
        ]);
    }

    public function test_filter_by_name_restaurant_eq(): void
    {
        $params = [
            'name[eq]' => 'two'
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

        $response->assertJsonPath('data.restaurants.0.name', 'B two');
        $response->assertJsonPath('data.restaurants.1.name', 'C two');
    }

    public function test_filter_by_description_restaurant_eq(): void
    {
        $params = [
            'description[eq]' => 'B two'
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

        $response->assertJsonPath('data.restaurants.0.description', 'B two');
        $response->assertJsonPath('data.restaurants.1.description', 'B two');
    }


}
