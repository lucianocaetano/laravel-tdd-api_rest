<?php

namespace Tests\Feature\Plate;

use App\Http\Resources\PlateResource;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlateSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListPlateTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    protected $restaurant;

    function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $this->restaurant = Restaurant::factory()->create();

        $this->seed(
            PlateSeeder::class
        );
    }

    protected $baseAPI = "api/v1";

    public function test_list_my_plates(): void
    {
        $restaurant = $this->restaurant;
        $user = $restaurant->user;
        $plates = $restaurant->plates()->paginate();

        $response = $this->apiAs($user, "get", $this->baseAPI . '/' . $restaurant->slug . '/plate');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'plates' => [
                    '*' => [
                        "id",
                        'name',
                        'description',
                        'price',
                        'image',
                        'restaurant',
                        'links' => [
                            'self',
                            'index',
                            'store',
                            'update',
                            'delete',
                        ]
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

        $response->assertJsonPath('data.meta.count', 10);
        $response->assertJsonPath('data.meta.current_page', 1);
        $response->assertJsonPath('data.meta.last_page', 1);
        $response->assertJsonPath('data.meta.per_page', 15);
        $response->assertJsonPath('data.meta.total', 10);

        $response->assertJsonPath('data.links.first', route('plate.index', ['restaurant' => $restaurant->slug, 'page' => 1]));
        $response->assertJsonPath('data.links.last', route('plate.index', ['restaurant' => $restaurant->slug, 'page' => $plates->lastPage()]));
        $prevPageUrl = $plates->currentPage() > 1 ? route('plate.index', ['restaurant' => $restaurant->slug, 'page' => $plates->currentPage() - 1]) : null;
        $nextPageUrl = $plates->hasMorePages() ? route('plate.index', ['restaurant' => $restaurant->slug, 'page' => $plates->currentPage() + 1]) : null;
        $response->assertJsonPath('data.links.prev', $prevPageUrl);
        $response->assertJsonPath('data.links.next', $nextPageUrl);

        $response->assertJsonPath('data.plates.0.links.self', route('plate.show', ['restaurant' => $restaurant->slug, 'plate' => $plates->first()->id]));
        $response->assertJsonPath('data.plates.0.links.index', route('plate.index', ['restaurant' => $restaurant->slug]));
        $response->assertJsonPath('data.plates.0.links.store', route('plate.store', ['restaurant' => $restaurant->slug]));
        $response->assertJsonPath('data.plates.0.links.update', route('plate.update', ['restaurant' => $restaurant->slug, 'plate' => $plates->first()->id]));
        $response->assertJsonPath('data.plates.0.links.delete', route('plate.destroy', ['restaurant' => $restaurant->slug, 'plate' => $plates->first()->id]));
        $response->assertJsonCount(10, "data.plates");
    }
}
