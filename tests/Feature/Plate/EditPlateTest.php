<?php

namespace Tests\Feature\Plate;

use App\Http\Resources\PlateResource;
use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\PlateSeeder;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditPlateTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();

        $this->seed([RestaurantSeeder::class, PlateSeeder::class, UserSeeder::class]);
    }

    protected $baseAPI = "api/v1";
    public $data = [
        'name' => "Milanesas",
    ];

    public function test_edit_my_plate(): void
    {
        $plate = Plate::first();
        $restaurant = $plate->restaurant;
        $user = $restaurant->user;

        $response = $this->apiAs($user, "put", $this->baseAPI . "/" . $restaurant->slug . '/plate/' . $plate->slug, $this->data);

        $response->assertStatus(200);
        $response->assertJsonFragment(["errors" => null]);
        $response->assertJsonFragment(["message" => "OK"]);

        $response->assertJsonStructure([
            'data' => [
                "plate" => [
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
                    ],
                ],
            ]
        ]);

        $response->assertJsonPath('data.plate.name', $this->data['name']);
        
        $response->assertJsonPath('data.plate.links.self', route('plate.show', ['restaurant' => $restaurant->slug, 'plate' => $plate->id]));
        $response->assertJsonPath('data.plate.links.self', route('plate.show', ['restaurant' => $restaurant->slug, 'plate' => $plate->id]));
        $response->assertJsonPath('data.plate.links.index', route('plate.index', ['restaurant' => $restaurant->slug]));
        $response->assertJsonPath('data.plate.links.store', route('plate.store', ['restaurant' => $restaurant->slug]));
        $response->assertJsonPath('data.plate.links.update', route('plate.update', ['restaurant' => $restaurant->slug, 'plate' => $plate->id]));
        $response->assertJsonPath('data.plate.links.delete', route('plate.destroy', ['restaurant' => $restaurant->slug, 'plate' => $plate->id]));    }

    public function test_you_are_not_the_owner_of_this_restaurant(): void
    {
        $plate = Plate::first();
        $restaurant = $plate->restaurant;
        $user = User::where("name", "=", "mauro")->first();

        $response = $this->apiAs($user, "delete", $this->baseAPI . "/" . $restaurant->slug . '/plate/' . $plate->slug);

        $response->assertStatus(403);
    }

    public function test_you_are_not_the_owner_of_this_plate(): void
    {
        $restaurant = Restaurant::first();
        $user = User::where("name", "=", "mauro")->first();
        $plate = Plate::whereNot("restaurant_id", $restaurant->id)->first();

        $response = $this->apiAs($user, "patch", $this->baseAPI . "/" . $restaurant->slug . "/plate/" . $plate->slug, $this->data);

        $response->assertStatus(403);
    }
}
