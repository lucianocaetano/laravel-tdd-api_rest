<?php

namespace Tests\Feature\Menu;

use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlateSeeder;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListMenuTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            RestaurantSeeder::class,
            MenuSeeder::class,
            UserSeeder::class,
            PlateSeeder::class,
        ]);
    }

    protected $baseAPI = "api/v1";

    public function test_list_menus_paginated(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;
        $menus = $restaurant->menus()->paginate();

        $response = $this->apiAs($user, "get", $this->baseAPI . "/" . $restaurant->slug . "/menu");

        $response->assertStatus(200);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);

        $response->assertJsonStructure([
            'data' => [
                "menus" => [
                    "*" => [
                        'name',
                        'slug',
                        'description',
                        'restaurant',
                        'links' => [
                            'self',
                            'index',
                            'store',
                            'update',
                            'delete',
                        ],
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    "total",
                    "count",
                    "per_page",
                    "current_page",
                    "last_page",
                ],
            ]
        ]);

        $response->assertJsonPath("data.meta.total", $menus->total());
        $response->assertJsonPath("data.meta.count", $menus->count());
        $response->assertJsonPath("data.meta.per_page", $menus->perPage());
        $response->assertJsonPath("data.meta.current_page", $menus->currentPage());
        $response->assertJsonPath("data.meta.last_page", $menus->lastPage());

        $response->assertJsonPath('data.links.first', route('menu.index', ['restaurant' => $restaurant->slug, 'page' => 1]));
        $response->assertJsonPath('data.links.last', route('menu.index', ['restaurant' => $restaurant->slug, 'page' => $menus->lastPage()]));
        $prevPageUrl = $menus->currentPage() > 1 ? route('menu.index', ['restaurant' => $restaurant->slug, 'page' => $menus->currentPage() - 1]) : null;
        $nextPageUrl = $menus->hasMorePages() ? route('menu.index', ['restaurant' => $restaurant->slug, 'page' => $menus->currentPage() + 1]) : null;
        $response->assertJsonPath('data.links.prev', $prevPageUrl);
        $response->assertJsonPath('data.links.next', $nextPageUrl);

        $response->assertJsonPath('data.menus.0.links.self', route('menu.show', ['restaurant' => $restaurant->slug, 'menu' => $menus->first()->id]));
        $response->assertJsonPath('data.menus.0.links.index', route('menu.index', ['restaurant' => $restaurant->slug]));
        $response->assertJsonPath('data.menus.0.links.store', route('menu.store', ['restaurant' => $restaurant->slug]));
        $response->assertJsonPath('data.menus.0.links.update', route('menu.update', ['restaurant' => $restaurant->slug, 'menu' => $menus->first()->id]));
        $response->assertJsonPath('data.menus.0.links.delete', route('menu.destroy', ['restaurant' => $restaurant->slug, 'menu' => $menus->first()->id]));
    }

    public function test_list_menus_paginated_2(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;
        $menus = $restaurant->menus()->paginate(15, ['*'], 'page', 2);

        $response = $this->apiAs($user, "get", $this->baseAPI . "/" . $restaurant->slug . "/menu?page=2");

        $response->assertStatus(200);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);

        $response->assertJsonStructure([
            'data' => [
                "menus" => [
                    "*" => [
                        'name',
                        'slug',
                        'description',
                        'restaurant',
                        'links' => [
                            'self',
                            'index',
                            'store',
                            'update',
                            'delete',
                        ],
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    "total",
                    "count",
                    "per_page",
                    "current_page",
                    "last_page",
                ],
            ]
        ]);

        $response->assertJsonPath("data.meta.total", $menus->total());
        $response->assertJsonPath("data.meta.count", $menus->count());
        $response->assertJsonPath("data.meta.per_page", $menus->perPage());
        $response->assertJsonPath("data.meta.current_page", $menus->currentPage());
        $response->assertJsonPath("data.meta.last_page", $menus->lastPage());

        $response->assertJsonPath('data.links.first', route('menu.index', ['restaurant' => $restaurant->slug, 'page' => 1]));
        $response->assertJsonPath('data.links.last', route('menu.index', ['restaurant' => $restaurant->slug, 'page' => $menus->lastPage()]));
        $prevPageUrl = $menus->currentPage() > 1 ? route('menu.index', ['restaurant' => $restaurant->slug, 'page' => $menus->currentPage() - 1]) : null;
        $nextPageUrl = $menus->hasMorePages() ? route('menu.index', ['restaurant' => $restaurant->slug, 'page' => $menus->currentPage() + 1]) : null;
        $response->assertJsonPath('data.links.prev', $prevPageUrl);
        $response->assertJsonPath('data.links.next', $nextPageUrl);

        $response->assertJsonPath('data.menus.0.links.self', route('menu.show', ['restaurant' => $restaurant->slug, 'menu' => $menus->first()->id]));
        $response->assertJsonPath('data.menus.0.links.index', route('menu.index', ['restaurant' => $restaurant->slug]));
        $response->assertJsonPath('data.menus.0.links.store', route('menu.store', ['restaurant' => $restaurant->slug]));
        $response->assertJsonPath('data.menus.0.links.update', route('menu.update', ['restaurant' => $restaurant->slug, 'menu' => $menus->first()->id]));
        $response->assertJsonPath('data.menus.0.links.delete', route('menu.destroy', ['restaurant' => $restaurant->slug, 'menu' => $menus->first()->id]));
    }

    public function test_you_are_not_the_owner_of_this_restaurant(): void
    {
        $restaurant = Restaurant::first();
        $user = User::where("name", "=", "mauro")->first();

        $response = $this->apiAs($user, "get", $this->baseAPI . "/" . $restaurant->slug . "/menu");

        $response->assertStatus(403);
    }
}
