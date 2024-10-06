<?php

namespace Tests\Feature\Plate;

use App\Models\Plate;
use App\Models\User;
use Database\Seeders\PlateSeeder;
use Database\Seeders\RestaurantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeletePlateTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    function setUp(): void {
        parent::setUp();

        $this->seed([RestaurantSeeder::class ,PlateSeeder::class]);
    }

    public $baseAPI = "/api/v1";

    public function test_delete_my_plate(): void
    {
        $plate = Plate::first();
        $restaurant = $plate->restaurant;
        $user = $restaurant->user;

        $response = $this->apiAs($user, "delete", $this->baseAPI . "/" . $restaurant->slug . '/plate/' . $plate->slug);

        $response->assertStatus(200);
        $response->assertJsonStructure(["errors", "message", "data"]);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonFragment(["data" => null]);
        $response->assertJsonFragment(["errors" => null]);
    }
}
