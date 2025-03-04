<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([UserSeeder::class, RestaurantSeeder::class]);
    }

    protected $data = ["name" => "New Restaurant", "description" => "test description form New Restaurant"];
    protected $baseAPI = "api/v1";

    public function test_a_restaurant_edit(): void
    {
        $restaurant = Restaurant::first();
        $user = $restaurant->user;

        $response = $this->apiAs($user, "patch", $this->baseAPI . '/restaurant/' . $restaurant->slug, $this->data);

        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "errors", "data" => ["restaurant" => ["name", "description", "slug", "user", 'links' => ['self', 'index', 'store', 'update', 'delete']]]]);
        $response->assertJsonFragment(["message" => "OK"]);
        $response->assertJsonFragment(["errors" => null]);

        $response->assertJsonPath('data.restaurant.links.self', route('restaurant.show', ['restaurant' => str($this->data['name'])->slug()->value()]));
        $response->assertJsonPath('data.restaurant.links.index', route('restaurant.index'));
        $response->assertJsonPath('data.restaurant.links.store', route('restaurant.store'));
        $response->assertJsonPath('data.restaurant.links.update', route('restaurant.update', str($this->data['name'])->slug()->value()));
        $response->assertJsonPath('data.restaurant.links.delete', route('restaurant.destroy', str($this->data['name'])->slug()->value()));
    }

    public function test_edit_not_my_restaurant(): void
    {
        $user = User::first();
        $restaurant = Restaurant::whereNot('user_id', $user->id)->first();

        $response = $this->apiAs($user, "put", $this->baseAPI . '/restaurant/' . $restaurant->slug, $this->data);

        $response->assertStatus(403);
    }
}
