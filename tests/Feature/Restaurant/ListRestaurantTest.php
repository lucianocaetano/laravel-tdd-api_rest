<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListRestaurantTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class
        ]);

        Restaurant::factory(5)->create([
            "user_id" => User::first()->id
        ]);
    }

    protected $baseAPI = "api/v1";

    public function test_list_my_restaurants(): void
    {
        $user = User::where(["name" => "mauro"])->first();
        $restaurants = $user->restaurants()->paginate();

        $response = $this->apiAs($user, "get", $this->baseAPI . '/restaurant');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'restaurants' => [
                    '*' => [
                        'name',
                        'description',
                        'slug',
                        'user',
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
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ],
            'message',
            'errors',
        ]);

        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);

        $response->assertJsonPath('data.meta.count', 5);
        $response->assertJsonPath('data.meta.current_page', 1);
        $response->assertJsonPath('data.meta.last_page', 1);
        $response->assertJsonPath('data.meta.per_page', 15);
        $response->assertJsonPath('data.meta.total', 5);

        $response->assertJsonPath('data.links.first', route('restaurant.index', ['page' => 1]));
        $response->assertJsonPath('data.links.last', route('restaurant.index', ['page' => $restaurants->lastPage()]));
        $prevPageUrl = $restaurants->currentPage() > 1 ? route('restaurant.index', ['page' => $restaurants->currentPage() - 1]) : null;
        $nextPageUrl = $restaurants->hasMorePages() ? route('restaurant.index', ['page' => $restaurants->currentPage() + 1]) : null;
        $response->assertJsonPath('data.links.prev', $prevPageUrl);
        $response->assertJsonPath('data.links.next', $nextPageUrl);

        $response->assertJsonPath('data.restaurants.0.links.self', route('restaurant.show', ['restaurant' => $restaurants->first()->slug]));
        $response->assertJsonPath('data.restaurants.0.links.index', route('restaurant.index'));
        $response->assertJsonPath('data.restaurants.0.links.store', route('restaurant.store'));
        $response->assertJsonPath('data.restaurants.0.links.update', route('restaurant.update', $restaurants->first()->slug));
        $response->assertJsonPath('data.restaurants.0.links.delete', route('restaurant.destroy', $restaurants->first()->slug));
    }
}
