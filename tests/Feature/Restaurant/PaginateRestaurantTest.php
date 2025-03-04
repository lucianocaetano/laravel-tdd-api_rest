<?php

namespace Tests\Feature\Restaurant;

use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaginateRestaurantTest extends TestCase
{
    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);

        Restaurant::factory(30)->create([
            "user_id" => User::first()->id
        ]);
    }

    protected $baseAPI = "api/v1";

    public function test_restaurant_pagination(): void
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

        $response->assertJsonPath('data.meta.count', $restaurants->count());
        $response->assertJsonPath('data.meta.current_page', $restaurants->currentPage());
        $response->assertJsonPath('data.meta.last_page', $restaurants->lastPage());
        $response->assertJsonPath('data.meta.per_page', $restaurants->perPage());
        $response->assertJsonPath('data.meta.total', $restaurants->total());

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

    public function test_restaurant_page_2(): void
    {
        $user = User::where(["name" => "mauro"])->first();
        $restaurants = $user->restaurants()->paginate(15, ['*'], 'page', 2);

        $response = $this->apiAs($user, "get", $this->baseAPI . '/restaurant?page=2');

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

        $response->assertJsonPath('data.meta.count', $restaurants->count());
        $response->assertJsonPath('data.meta.current_page', $restaurants->currentPage());
        $response->assertJsonPath('data.meta.last_page', $restaurants->lastPage());
        $response->assertJsonPath('data.meta.per_page', $restaurants->perPage());
        $response->assertJsonPath('data.meta.total', $restaurants->total());

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
