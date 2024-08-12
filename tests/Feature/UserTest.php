<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    protected $baseAPI = "api/v1/user";
    protected $data = ["name" => "mauro 2",];

    public function test_an_authenticated_user_can_modify_their_data(): void
    {
        $user = User::first();
        $response = $this->apiAs($user, "patch", $this->baseAPI . '/update', $this->data);

        $response->assertStatus(200);
        $response->assertJsonStructure(["data", "message", "errors"]);

        $response->assertJsonFragment([
            "message" => "OK",
            "errors" => null,
            "data" => [
                "user" => [
                    "id" => $user->id,
                    "last_name" => $user->last_name,
                    "name" => "mauro 2",
                    "email" => $user->email,
                    "email_verified_at" => $user->email_verified_at,
                    "created_at" => $user->created_at,
                    "updated_at" => $user->updated_at,
                ]
            ],
        ]);
    }

    public function test_get_my_user() {
        $user = User::first();
        $response = $this->apiAs($user, "get", $this->baseAPI . '/me');

        $response->assertStatus(200);
        $response->assertJsonStructure(["data", "message", "errors"]);
        $response->assertJsonFragment([
            "message" => "OK",
        ]);

        $response->assertJsonFragment([
            "errors" => null,
        ]);

        $response->assertJsonFragment([
            "data" => [
                "user" => $user->toArray()
            ],
        ]);
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
