<?php

namespace Tests\Feature\User;

use App\Enums\Roles;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin, $user;

    protected $baseAPI = '/api/v1';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $this->admin = User::factory()->create([]);
        $this->admin->assignRole(Roles::ADMIN->value);

        $this->user = User::factory()->create([]);
    }

    public function test_valid_type_name_field(): void
    {
        $admin = $this->admin;

        $data = [
            'name' => 3,
            'last_name' => 'tester',
            'email' => 'test@test.com',
            'role' => 'user',
            'password' => 'password1234',

        ];

        $response = $this->apiAs($admin, "post", $this->baseAPI . '/dashboard/users/', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['name']);
    }

    public function test_required_name_field(): void
    {
        $admin = $this->admin;

        $data = [
            'last_name' => 'tester',
            'email' => 'test@test.com',
            'role' => 'user',
            'password' => 'password1234',
        ];

        $response = $this->apiAs($admin, "post", $this->baseAPI . '/dashboard/users/', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['name']);
    }

    public function test_valid_type_last_name_field(): void
    {
        $admin = $this->admin;

        $data = [
            'name' => 'test',
            'last_name' => 34,
            'email' => 'test@test.com',
            'role' => 'user',
            'password' => 'password1234',

        ];

        $response = $this->apiAs($admin, "post", $this->baseAPI . '/dashboard/users/', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['last_name']);
    }

    public function test_required_last_name_field(): void
    {
        $admin = $this->admin;

        $data = [
            "name" => 'test',
            'email' => 'test@test.com',
            'role' => 'user',
            'password' => 'password1234',
        ];

        $response = $this->apiAs($admin, "post", $this->baseAPI . '/dashboard/users/', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['last_name']);
    }

    public function test_valid_type_email_field(): void
    {
        $admin = $this->admin;

        $data = [
            'name' => 'test',
            'last_name' => 34,
            'email' => 23,
            'role' => 'user',
            'password' => 'password1234',

        ];

        $response = $this->apiAs($admin, "post", $this->baseAPI . '/dashboard/users/', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['email']);
    }

    public function test_required_email_field(): void
    {
        $admin = $this->admin;

        $data = [
            "name" => 'test',
            "last_name" => 'test',
            'role' => 'user',
            'password' => 'password1234',
        ];

        $response = $this->apiAs($admin, "post", $this->baseAPI . '/dashboard/users/', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['email']);
    }

    public function test_valid_format_email_field(): void
    {
        $admin = $this->admin;

        $data = [
            "name" => 'test',
            "email" => 'test',
            "last_name" => 'test',
            'role' => 'user',
            'password' => 'password1234',
        ];

        $response = $this->apiAs($admin, "post", $this->baseAPI . '/dashboard/users/', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['email']);
    }

    public function test_valid_type_role_field(): void
    {
        $admin = $this->admin;

        $data = [
            'name' => 'test',
            'last_name' => 'test',
            'email' => 'test',
            'role' => 34,
            'password' => 'password1234',
        ];

        $response = $this->apiAs($admin, "post", $this->baseAPI . '/dashboard/users/', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['role']);
    }

    public function test_valid_value_role_field(): void
    {
        $admin = $this->admin;

        $data = [
            "name" => 'test',
            "email" => 'test@test.com',
            "last_name" => 'test',
            'role' => 'test',
            'password' => 'password1234',
        ];

        $response = $this->apiAs($admin, "post", $this->baseAPI . '/dashboard/users/', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['role']);
    }

    public function test_valid_type_password_field(): void
    {
        $admin = $this->admin;

        $data = [
            "name" => 'test',
            "email" => 'test@gmail.com',
            "last_name" => 'test',
            'role' => 'user',
            'password' => 50,
        ];

        $response = $this->apiAs($admin, "post", $this->baseAPI . '/dashboard/users/', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['password']);
    }

    public function test_required_password_field(): void
    {
        $admin = $this->admin;

        $data = [
            "name" => 'test',
            "email" => 'test@gmail.com',
            "last_name" => 'test',
            'role' => 'user',
        ];

        $response = $this->apiAs($admin, "post", $this->baseAPI . '/dashboard/users/', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['password']);
    }

    public function test_max_char_password_field(): void
    {
        $admin = $this->admin;

        $data = [
            "name" => 'test',
            "email" => 'test@gmail.com',
            "last_name" => 'test',
            'role' => 'user',
            'password' => str_repeat('a', 51)
        ];

        $response = $this->apiAs($admin, "post", $this->baseAPI . '/dashboard/users/', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['password']);
    }

    public function test_hashed_password_field(): void
    {
        $admin = $this->admin;

        $data = [
            "name" => 'test',
            "email" => 'test@gmail.com',
            "last_name" => 'test',
            'role' => 'user',
            'password' => 'password1234'
        ];

        $response = $this->apiAs($admin, "post", $this->baseAPI . '/dashboard/users/', $data);

        $response->assertStatus(200);

        $user = User::where('email', $data['email'])->first();

        $this->assertTrue(Hash::check($data['password'], $user->password));
    }

    public function test_admin_create_one_user(): void
    {
        $admin = $this->admin;

        $data = [
            'name' => 'test',
            'last_name' => 'tester',
            'email' => 'test@test.com',
            'role' => 'user',
            'password' => 'password1234',
        ];

        $response = $this->apiAs($admin, "post", $this->baseAPI . '/dashboard/users/', $data);

        $response->assertStatus(200);
    }

    public function test_user_create_one_user(): void
    {
        $user = $this->user;

        $data = [
            "name" => 'test',
            "email" => 'test@test.com',
            "last_name" => 'test',
            'password' => 'password1234',
        ];

        $response = $this->apiAs($user, "post", $this->baseAPI . '/dashboard/users/', $data);

        $response->assertStatus(403);
    }

}
