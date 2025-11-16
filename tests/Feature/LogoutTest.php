<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class
        ]);
    }
    protected $baseAPI = "api/v1/";

    public function test_logout_user(): void
    {
        $user = User::first();
        $response = $this->apiAs($user, "post", $this->baseAPI . "logout/");

        $response->assertStatus(200);
        $response->assertJson(["message" => "OK"]);
    }
}
