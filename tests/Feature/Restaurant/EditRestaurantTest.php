<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RestaurantSeeder::class, UserSeeder::class]);
    }

    protected $data = ["name" => "New Restaurant", "description" => "test description form New Restaurant"];
    protected $baseAPI = "api/v1";

    public function test_a_restaurant_edit(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;

        $response = $this->apiAs($user, "put", $this->baseAPI . '/restaurant/' . $restaurant->slug, $this->data);

        $response->assertStatus(200);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonStructure(["message", "errors", "data" => ["restaurant" => ["name", "description", "slug", "user_id"]]]);
    }

    public function test_edit_not_my_restaurant(): void
    {
        $restaurant = Restaurant::first();
        $user = User::where(["name" => "mauro"])->first();

        $response = $this->apiAs($user, "put", $this->baseAPI . '/restaurant/' . $restaurant->slug, $this->data);

        $response->assertStatus(403);
    }
}
