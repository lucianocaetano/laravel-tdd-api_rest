<?php

namespace Tests\Feature\AdminUser;

use App\Enums\Roles;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListUserTest extends TestCase
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

        User::factory(30)->create();
    }

    public function test_admin_get_one_user(): void
    {
        $admin = $this->admin;
        $users = User::paginate();

        $response = $this->apiAs($admin, "get", $this->baseAPI . '/dashboard/users/');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'errors' => null,
        ]);

        $response->assertJsonFragment([
            'message' => 'OK',
        ]);

        $response->assertJsonStructure([
            'data' => [
                'users' => [
                    '*' => [
                        "id",
                        "name",
                        "last_name",
                        "roles",
                        "email",
                        "email_verified_at"
                    ]
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next'
                ],
                'meta' => [
                    "total",
                    "count",
                    "per_page",
                    "current_page",
                    "last_page"
                ]
            ]
        ]);

        $response->assertJsonPath('data.meta.count', 15);
        $response->assertJsonPath('data.meta.current_page', 1);
        $response->assertJsonPath('data.meta.last_page', 3);
        $response->assertJsonPath('data.meta.per_page', 15);
        $response->assertJsonPath('data.meta.total', 32);

        $response->assertJsonPath('data.links.first', route('dashboard.users.index', ['page' => 1]));
        $response->assertJsonPath('data.links.last', route('dashboard.users.index', ['page' => $users->lastPage()]));
        $prevPageUrl = $users->currentPage() > 1 ? route('dashboard.users.index', ['page' => $users->currentPage() - 1]) : null;
        $nextPageUrl = $users->hasMorePages() ? route('dashboard.users.index', ['page' => $users->currentPage() + 1]) : null;
        $response->assertJsonPath('data.links.prev', $prevPageUrl);
        $response->assertJsonPath('data.links.next', $nextPageUrl);
    }

    public function test_admin_get_one_user_page_2(): void
    {
        $admin = $this->admin;
        $users = User::paginate(15, ['*'], 'page', 2);

        $response = $this->apiAs($admin, "get", $this->baseAPI . '/dashboard/users?page=2');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'errors' => null,
        ]);

        $response->assertJsonFragment([
            'message' => 'OK',
        ]);

        $response->assertJsonStructure([
            'data' => [
                'users' => [
                    '*' => [
                        "id",
                        "name",
                        "last_name",
                        "roles",
                        "email",
                        "email_verified_at"
                    ]
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next'
                ],
                'meta' => [
                    "total",
                    "count",
                    "per_page",
                    "current_page",
                    "last_page"
                ]
            ]
        ]);

        $response->assertJsonPath('data.meta.count', 15);
        $response->assertJsonPath('data.meta.current_page', 2);
        $response->assertJsonPath('data.meta.last_page', 3);
        $response->assertJsonPath('data.meta.per_page', 15);
        $response->assertJsonPath('data.meta.total', 32);

        $response->assertJsonPath('data.links.first', route('dashboard.users.index', ['page' => 1]));
        $response->assertJsonPath('data.links.last', route('dashboard.users.index', ['page' => $users->lastPage()]));
        $prevPageUrl = $users->currentPage() > 1 ? route('dashboard.users.index', ['page' => $users->currentPage() - 1]) : null;
        $nextPageUrl = $users->hasMorePages() ? route('dashboard.users.index', ['page' => $users->currentPage() + 1]) : null;
        $response->assertJsonPath('data.links.prev', $prevPageUrl);
        $response->assertJsonPath('data.links.next', $nextPageUrl);
    }

    public function test_normal_user_get_one_user(): void
    {
        $user = $this->user;

        $response = $this->apiAs($user, "get", $this->baseAPI . '/dashboard/users/');

        $response->assertStatus(403);
    }
}
