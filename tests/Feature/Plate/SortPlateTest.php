<?php

namespace Tests\Feature\Plate;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SortPlateTest extends TestCase
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

        Plate::factory()->create([
            "restaurant_id" => $this->restaurant->id,
            'name' => "A one",
            'description' => "A one",
        ]);

        Plate::factory()->create([
            "restaurant_id" => $this->restaurant->id,
            'name' => "B two",
            'description' => "B two",
        ]);

        Plate::factory()->create([
            "restaurant_id" => $this->restaurant->id,
            'name' => "C tree",
            'description' => "C tree"
        ]);
    }


    public function test_sort_by_name_plates(): void
    {
        $params = [
            'sort' => 'name'
        ];

        $response = $this->apiAs($this->user, 'get', route('plate.index', $this->restaurant->slug), $params);

        $response->assertStatus(200);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);

        $response->assertJsonStructure([
            'data' => [
                'plates' => [
                    '*' => [
                        'name',
                        'description',
                        'slug',
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

        $response->assertJsonPath('data.plates.0.name', 'A one');
        $response->assertJsonPath('data.plates.1.name', 'B two');
        $response->assertJsonPath('data.plates.2.name', 'C tree');
    }

    public function test_sort_by_description_plates(): void
    {
        $params = [
            'sort' => 'description'
        ];

        $response = $this->apiAs($this->user, 'get', route('plate.index', $this->restaurant->slug), $params);

        $response->assertStatus(200);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);

        $response->assertJsonStructure([
            'data' => [
                'plates' => [
                    '*' => [
                        'name',
                        'description',
                        'slug',
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

        $response->assertJsonPath('data.plates.0.description', 'A one');
        $response->assertJsonPath('data.plates.1.description', 'B two');
        $response->assertJsonPath('data.plates.2.description', 'C tree');
    }

}
