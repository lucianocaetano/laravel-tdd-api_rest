<?php

namespace Tests\Feature\User;

use App\Enums\Roles;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteUserTest extends TestCase
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

    /**
     * A basic feature test example.
     */
    public function test_admin_delete_any_user(): void
    {
        $admin = $this->admin;
        $user = $this->user;

        $response = $this->apiAs($admin, "delete", $this->baseAPI . '/dashboard/users/' . $user->id);

        $response->assertStatus(200);
    }

    public function test_user_delete_any_user(): void
    {
        $admin = $this->admin;
        $user = $this->user;

        $response = $this->apiAs($user, "delete", $this->baseAPI . '/dashboard/users/' . $admin->id);

        $response->assertStatus(403);
    }
}
