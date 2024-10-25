<?php

namespace Tests\Feature\Plate;

use App\Http\Resources\PlateResource;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\PlateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlateListTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();

        Restaurant::factory()->create();
        $this->seed(PlateSeeder::class);

    }

    protected $baseAPI = "api/v1";

    public function test_list_my_plates(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;
        $plates = $restaurant->plates()->paginate();

        $response = $this->apiAs($user, "get", $this->baseAPI . '/' . $restaurant->slug . '/plate');

        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "errors", "data"]);
        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonFragment(["data" => [
            "plates" => PlateResource::collection($plates)->resolve(),
            "total" => 10,
            "count" => 10,
            "per_page" => 15,
            "current_page" => 1,
            "last_page" => 1
        ]
        ]);
        $response->assertJsonCount(10, "data.plates");
    }
}
