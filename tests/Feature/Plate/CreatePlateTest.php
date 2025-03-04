<?php

namespace Tests\Feature\Plate;

use App\Models\Restaurant;
use Database\Seeders\RestaurantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreatePlateTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();

        $this->seed(RestaurantSeeder::class);
    }

    protected $baseAPI = "api/v1";

    public function test_create_plates(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;

        $data = [
            'name' => "Milanesas",
            'description' => "descripcion generica",
            'price' => 200,
            'image' => null
        ];

        $response = $this->apiAs($user, "post", $this->baseAPI . '/' . $restaurant->slug . '/plate', $data);

        $response->assertStatus(200);
        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);

        $response->assertJsonStructure([
            'data' => [
                "plate" => [
                    "id",
                    'name',
                    'description',
                    'price',
                    'image',
                    'restaurant',
                    'links' => [
                        'self',
                        'index',
                        'store',
                        'update',
                        'delete',
                    ],
                ],
            ]
        ]);

        $plate = $response->json()['data']['plate'];
        
        $response->assertJsonPath('data.plate.links.self', route('plate.show', ['restaurant' => $restaurant->slug, 'plate' => $plate["id"]]));
        $response->assertJsonPath('data.plate.links.index', route('plate.index', ['restaurant' => $restaurant->slug]));
        $response->assertJsonPath('data.plate.links.store', route('plate.store', ['restaurant' => $restaurant->slug]));
        $response->assertJsonPath('data.plate.links.update', route('plate.update', ['restaurant' => $restaurant->slug, 'plate' => $plate["id"]]));
        $response->assertJsonPath('data.plate.links.delete', route('plate.destroy', ['restaurant' => $restaurant->slug, 'plate' => $plate["id"]]));


        $this->assertDatabaseCount("plates", 1);
    }
}
