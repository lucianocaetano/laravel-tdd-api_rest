<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    
    protected $baseAPI = "api/v1/auth/";
    protected $data = ["name" => "mauro", "email" => "lucianocaetano@gmail.com", "password" => "password123"];
   
    public function test_an_auth(): void
    {
        $response = $this->postJson($this->baseAPI.'register/', $this->data);
        
        $response->assertStatus(201);
        $response->assertJsonStructure(["data" => ["token"]]);
    }

    public function test_email_must_be_required(): void
    {
        $data = $this->data;
        $data["email"] = "";

        $response = $this->postJson($this->baseAPI.'register/', $data);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            "errors" => ["email"]
        ]);

    }

    public function test_password_must_be_required(): void
    {
        $data = $this->data;
        $data["password"] = "";

        $response = $this->postJson($this->baseAPI.'register/', $data);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            "errors" => ["password"]
        ]);

    }

    public function test_name_must_be_required(): void
    {
        $data = $this->data;
        $data["name"] = "";

        $response = $this->postJson($this->baseAPI.'register/', $data);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            "errors" => ["name"]
        ]);

    }

}
