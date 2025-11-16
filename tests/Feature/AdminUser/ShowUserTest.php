<?php

namespace Tests\Feature\AdminUser;

use App\Enums\Roles;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowUserTest extends TestCase
{
    use RefreshDatabase;

    protected $user, $admin;
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

    public function test_admin_get_one_user(): void
    {
        $admin = $this->admin;
        $user = $this->user;

        $response = $this->apiAs($admin, "get", $this->baseAPI . '/dashboard/users/' . $user->id);

        $response->assertStatus(200);

        $response->assertJsonPath('data.user.email', $user->email);
    }

    public function test_normal_user_get_one_user(): void
    {
        $admin = $this->admin;
        $user = $this->user;

        $response = $this->apiAs($user, "get", $this->baseAPI . '/dashboard/users/' . $admin->id);

        $response->assertStatus(403);
    }
}
