<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    protected $baseAPI = "api/v1/auth/";
    protected $credentials = ["email"=>"lucianocaetano@gmail.com", "password"=>"password123"];

    public function test_an_existing_user_can_login(): void
    {

        $response = $this->postJson($this->baseAPI.'login/', $this->credentials);

        $response->assertStatus(200);
        $response->assertJsonStructure(["data" => ["token"]]);
    }

    public function test_a_not_existing_user_cannot_login(): void
    {
        $credentials = $this->credentials;
        $credentials["email"] = "usuarioNoExiste@gmail.com";

        $response = $this->postJson($this->baseAPI.'login/', $credentials);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_email_must_be_required(): void
    {
        $credentials = $this->credentials;
        $credentials["email"] = "";

        $response = $this->postJson($this->baseAPI.'login/', $credentials);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            "errors" => ["email"]
        ]);

    }

    public function test_password_must_be_required(): void
    {
        $credentials = $this->credentials;
        $credentials["password"] = "";

        $response = $this->postJson($this->baseAPI.'login/', $credentials);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            "errors" => ["password"]
        ]);

    }
}
