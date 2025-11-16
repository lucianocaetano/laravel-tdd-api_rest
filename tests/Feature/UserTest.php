<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class
        ]);
    }

    protected $baseAPI = "api/v1/user";
    protected $data = ["name" => "mauro 2",];

    public function test_an_authenticated_user_can_modify_their_data(): void
    {
        $user = User::first();
        $response = $this->apiAs($user, "patch", $this->baseAPI . '/update', $this->data);

        $response->assertStatus(200);
        $response->assertJsonStructure(["data", "message", "errors"]);

        $response->assertJsonStructure([
            "data" => [
                "user" => [
                    "id",
                    "last_name",
                    "name",
                    "email",
                    "email_verified_at",
                    "created_at",
                    "updated_at",
                ]
            ],
        ]);

        $response->assertJsonPath('data.user.name', $this->data["name"]);
    }

    public function test_get_my_user() {
        $user = User::first();
        $response = $this->apiAs($user, "get", $this->baseAPI . '/me');

        $response->assertStatus(200);

        $response->assertJsonStructure(['data', 'errors', 'message']);

        $response->assertJsonStructure([
            "data" => [
                "user" => [
                    "id",
                    "last_name",
                    "name",
                    "email",
                    "email_verified_at",
                ]
            ],
        ]);

        $response->assertJsonPath('data.user.email', $user->email);
    }

    public function test_delete_my_user() {
        $user = User::first();
        $password = $user->password;

        $response = $this->apiAs($user, "delete", $this->baseAPI . '/remove', ["password" => $password]);

        $response->assertStatus(200);
        $response->assertJsonStructure(["data", "message", "errors"]);
        $response->assertJsonFragment([
            "message" => "OK",
            "errors" => null,
            "data" => null,
        ]);
    }
}
