<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteRestaurantTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();

        $this->seed([UserSeeder::class]);
        Restaurant::factory()->create();
    }

    protected $baseAPI = "api/v1";

    public function test_delete_my_restaurant(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;

        $response = $this->apiAs($user, "delete", $this->baseAPI . '/restaurant/' . $restaurant->slug);

        $response->assertStatus(200);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonStructure(["message", "errors", "data"]);
        $this->assertDatabaseCount("restaurants", 0);
    }

    public function test_delete_not_my_restaurant(): void
    {
        $restaurant = Restaurant::first();
        $user = User::where(["email" => "lucianocaetano@gmail.com"])->first();

        $response = $this->apiAs($user, "delete", $this->baseAPI . '/restaurant/' . $restaurant->slug);

        $response->assertStatus(403);
        $this->assertDatabaseCount("restaurants", 1);
    }

}
