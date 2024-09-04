<?php

namespace Tests\Feature\Restaurant;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    protected $data = ["name" => "New Restaurant", "description" => "test description form New Restaurant"];
    protected $baseAPI = "api/v1";

    public function test_a_restaurant_register(): void
    {
        $user = User::first();
        $response = $this->apiAs($user, "post", $this->baseAPI . '/restaurant', $this->data);

        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonStructure(["message", "errors", "data" => ["restaurant" => ["id", "name", "description", "slug", "user_id"]]]);
        $response->assertStatus(201);
    }
}
