<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RestaurantSeeder::class);
    }

    protected $data = ["name" => "New Restaurant", "description" => "test description form New Restaurant"];
    protected $baseAPI = "api/v1";

    public function test_a_restaurant_edit(): void
    {
        $user = User::first();
        $restaurant = Restaurant::first();

        $response = $this->apiAs($user, "put", $this->baseAPI . '/restaurant/' . $restaurant->slug, $this->data);

        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonStructure(["message", "errors", "data" => ["restaurant" => ["id", "name", "description", "slug", "user_id"]]]);
        $response->assertStatus(201);
    }
}
