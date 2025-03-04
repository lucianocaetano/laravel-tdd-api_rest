<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();

        $user->assignRole('admin');

        Restaurant::factory()->create();
    }

    protected $baseAPI = "/api/v1";

    public function test_admin_user_can_delete_any_restaurants(): void
    {
        $restaurant = Restaurant::first();
        $user = User::where(["email" => "lucianocaetano@gmail.com"])->first();

        $response = $this->apiAs($user, "delete", $this->baseAPI . '/restaurant/' . $restaurant->slug);

        $response->assertStatus(200);
        $this->assertDatabaseCount("restaurants", 0);
    }
}
