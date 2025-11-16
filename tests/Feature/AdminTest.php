<?php

namespace Tests\Feature;

use App\Enums\Roles;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected $user, $restaurant;

    function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class
        ]);

        $user = User::factory()->create();

        $user->assignRole(Roles::ADMIN->value);

        $restaurant = Restaurant::factory()->create();

        $this->user = $user;

        $this->restaurant = $restaurant;
    }

    protected $baseAPI = "/api/v1";

    public function test_admin_user_can_delete_any_restaurants(): void
    {
        $restaurant = $this->restaurant;
        $user = $this->user;

        $response = $this->apiAs($user, "delete", $this->baseAPI . '/restaurant/' . $restaurant->slug);

        $response->assertStatus(200);
        $this->assertDatabaseCount("restaurants", 0);
    }
}
