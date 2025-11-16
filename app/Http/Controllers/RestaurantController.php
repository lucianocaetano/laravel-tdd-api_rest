<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Http\Resources\RestaurantCollection;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class RestaurantController extends Controller
{
    /**
     *  @OA\Get(
     *      path="/api/v1/restaurant",
     *      summary="Get a list restaurants",
     *      tags={"Restaurants"},
     *      security={{"apiAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *             @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="restaurants",
     *                      type="array",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(property="name", type="string", example="Prof. Albert Rolfson DDS"),
     *                          @OA\Property(property="description", type="string", example="Ipsam totam officia incidunt alias quam et quos. Quisquam culpa est ut. Repellendus accusantium quis quia doloribus."),
     *                          @OA\Property(property="slug", type="string", example="prof-albert-rolfson-dds"),
     *                          @OA\Property(property="user", type="string", example="admin"),
     *                          @OA\Property(
     *                              property="links",
     *                              type="object",
     *                              @OA\Property(property="self", type="string", format="uri", example="http://127.0.0.1:8000/api/v1/restaurant/prof-albert-rolfson-dds"),
     *                              @OA\Property(property="index", type="string", format="uri", example="http://127.0.0.1:8000/api/v1/restaurant"),
     *                              @OA\Property(property="store", type="string", format="uri", example="http://127.0.0.1:8000/api/v1/restaurant"),
     *                              @OA\Property(property="update", type="string", format="uri", example="http://127.0.0.1:8000/api/v1/restaurant/prof-albert-rolfson-dds"),
     *                              @OA\Property(property="delete", type="string", format="uri", example="http://127.0.0.1:8000/api/v1/restaurant/prof-albert-rolfson-dds")
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *      )
     *  )
     */
    public function index()
    {
        $restaurants = Auth::user()
            ->restaurants()
            ->filter()
            ->sort()
            ->search(field: 'description')
            ->paginate();

        return jsonResponse(data: new RestaurantCollection($restaurants), message: "OK");
    }

    /**
     *  @OA\Post(
     *      path="/api/v1/restaurant",
     *      summary="Store a restaurant",
     *      tags={"Restaurants"},
     *      security={{"apiAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "description"},
     *              @OA\Property(property="name", type="string", example="Prof. Albert Rolfson DDS"),
     *              @OA\Property(property="description", type="string", example="Ipsam totam officia incidunt alias quam et quos."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Created",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="restaurant",
     *                      type="object",
     *                      @OA\Property(property="name", type="string", example="Prof. Albert Rolfson DDS"),
     *                      @OA\Property(property="description", type="string", example="Ipsam totam officia incidunt alias quam et quos. Quisquam culpa est ut. Repellendus accusantium quis quia doloribus."),
     *                      @OA\Property(property="slug", type="string", example="prof-albert-rolfson-dds"),
     *                      @OA\Property(property="user", type="string", example="admin"),
     *                      @OA\Property(
     *                          property="links",
     *                          type="object",
     *                          @OA\Property(property="self", type="string", format="uri", example="http://127.0.0.1:8000/api/v1/restaurant/prof-albert-rolfson-dds"),
     *                          @OA\Property(property="index", type="string", format="uri", example="http://127.0.0.1:8000/api/v1/restaurant"),
     *                          @OA\Property(property="store", type="string", format="uri", example="http://127.0.0.1:8000/api/v1/restaurant"),
     *                          @OA\Property(property="update", type="string", format="uri", example="http://127.0.0.1:8000/api/v1/restaurant/prof-albert-rolfson-dds"),
     *                          @OA\Property(property="delete", type="string", format="uri", example="http://127.0.0.1:8000/api/v1/restaurant/prof-albert-rolfson-dds")
     *                      )
     *                  )
     *              )
     *         )
     *      )
     *  )
     */
    public function store(StoreRestaurantRequest $request)
    {
        $data = $request->validated();
        $restaurant = Auth::user()->restaurants()->create($data);
        return jsonResponse(data: ["restaurant" => RestaurantResource::make($restaurant)], message: "OK", status: 201);
    }

    /**
     *  @OA\Get(
     *      path="/api/v1/restaurant/{restaurant}",
     *      summary="Get a restaurant",
     *      tags={"Restaurants"},
     *      security={{"apiAuth":{}}},
     *      @OA\Parameter(
     *          name="restaurant",
     *          in="path",
     *          required=true,
     *          description="slug restaurant",
     *          example="name-slug",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Show restaurant",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="restaurant",
     *                      type="object",
     *                      @OA\Property(property="name", type="string", example="Prof. Albert Rolfson DDS"),
     *                      @OA\Property(property="description", type="string", example="Ipsam totam officia incidunt alias quam et quos. Quisquam culpa est ut."),
     *                      @OA\Property(property="slug", type="string", example="prof-albert-rolfson-dds"),
     *                      @OA\Property(property="user", type="string", example="admin"),
     *                      @OA\Property(
     *                          property="links",
     *                          type="object",
     *                          @OA\Property(property="self", type="string", format="uri", example="http://127.0.0.1:8000/api/v1/restaurant/prof-albert-rolfson-dds"),
     *                          @OA\Property(property="index", type="string", format="uri", example="http://127.0.0.1:8000/api/v1/restaurant"),
     *                          @OA\Property(property="store", type="string", format="uri", example="http://127.0.0.1:8000/api/v1/restaurant"),
     *                          @OA\Property(property="update", type="string", format="uri", example="http://127.0.0.1:8000/api/v1/restaurant/prof-albert-rolfson-dds"),
     *                          @OA\Property(property="delete", type="string", format="uri", example="http://127.0.0.1:8000/api/v1/restaurant/prof-albert-rolfson-dds")
     *                      )
     *                  )
     *              )
     *         )
     *      )
     * )
     */
    public function show(Restaurant $restaurant)
    {
        Gate::authorize("view", $restaurant);
        return jsonResponse(data: ["restaurant" => RestaurantResource::make($restaurant)], message: "OK");
    }

    /**
     * @OA\Put(
     *     path="/api/v1/restaurant/{id}",
     *     summary="Updated a restaurant",
     *     tags={"Restaurants"},
     *     security={{"apiAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID the restaurant",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Show restaurant",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="restaurant",
     *                      type="object",
     *                      ref="#/components/schemas/Restaurant"
     *                  )
     *              )
     *         )
     *      )
     * )
     */
    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant)
    {
        Gate::authorize("update", $restaurant);
        $data = $request->validated();
        $restaurant->update($data);
        return jsonResponse(data: ["restaurant" => RestaurantResource::make($restaurant)], message: "OK");
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/restaurant/{id}",
     *     summary="Destroy a restaurant",
     *     tags={"Restaurants"},
     *     security={{"apiAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID the restaurant",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="delete",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="OK"
     *              )
     *         )
     *     )
     * )
     */
    public function destroy(Restaurant $restaurant)
    {
        Gate::authorize("delete", $restaurant);
        $restaurant->delete();
        return jsonResponse(message: "OK");
    }
}

