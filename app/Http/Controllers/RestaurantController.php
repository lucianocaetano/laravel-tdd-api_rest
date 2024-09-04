<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRestaurantRequest $request)
    {
        $data = $request->validated();

        $restaurant = Auth::user()->restaurants()->create($data);

        return jsonResponse(data: [
            "restaurant" => RestaurantResource::make($restaurant)
        ], message: "OK", status: 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant)
    {
        Gate::authorize("view", $restaurant);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant)
    {
        Gate::authorize("update", $restaurant);

        $data = $request->validated();

        $restaurant->update($data);

        return jsonResponse(data: [
            "restaurant" => RestaurantResource::make($restaurant)
        ], message: "OK", status: 201);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant)
    {
        Gate::authorize("delete", $restaurant);
    }
}
