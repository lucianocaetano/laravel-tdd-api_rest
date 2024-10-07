<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Models\Restaurant;

class MenuController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Restaurant $restaurant, StoreMenuRequest $request)
    {
        $request->validate();

        $menu = $restaurant->menu()->create($request->only("name", "description", "slug", "restaurant_id"));

        $menu->plates()->sync($request->get("plate_ids"));

        return jsonResponse(data: [
            "menu" => MenuResource::make($menu)
        ], message: "OK", status: 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        return jsonResponse(data: [
            "menu" => MenuResource::make($menu)
        ], message: "OK");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
