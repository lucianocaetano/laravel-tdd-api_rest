<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class
        ]);
    }

    protected $baseAPI = "api/v1";
    protected $data = ["password" => "new password", "password_valid" => "new password", "old_password" => "password123"];

    public function test_change_my_password(): void
    {
        $user = User::first();

        $response = $this->apiAs($user, 'patch', $this->baseAPI . "/change_password", $this->data);

        // vuelvo a pedir al usuario
        $user = User::first();

        $response->assertStatus(200);
        $response->assertJsonStructure(["data", "message", "errors"]);
        $response->assertJsonFragment(["data" => null, "message" => "OK", "errors" => null]);
        $this->assertTrue(Hash::check($this->data["password"], $user->password));
    }
}
