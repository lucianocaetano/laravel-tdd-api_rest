<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Resources\MenuCollection;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Gate;

class MenuController extends Controller
{

    public function index(Restaurant $restaurant){

        Gate::authorize("view", $restaurant);

        $menu = $restaurant->menus()->paginate();

        return jsonResponse(data:  new MenuCollection($menu), message: "OK", status: 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Restaurant $restaurant, StoreMenuRequest $request)
    {
        Gate::authorize("view", $restaurant);

        $request->validated();

        $menu = $restaurant->menus()->create($request->only("name", "description", "slug", "restaurant_id"));

        $menu->plates()->sync($request->get("plate_ids"));

        return jsonResponse(data: [
            "menu" => MenuResource::make($menu)
        ], message: "OK", status: 201);
    }

    public function show(Restaurant $restaurant, string $menu)
    {
        Gate::authorize("view", $restaurant);

        $menu = Menu::where('slug', $menu)->where('restaurant_id', $restaurant->id)->firstOrFail();

        return jsonResponse(data: [
            "menu" => MenuResource::make($menu)
        ], message: "OK");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request, Restaurant $restaurant, Menu $menu)
    {
        Menu::where('slug', $menu->slug)->where('restaurant_id', $restaurant->id)->firstOrFail();

        Gate::authorize("view", $restaurant);

        $request->validated();

        $menu->update($request->except("plate_ids"));

        if($request->get("plate_ids")){
            $menu->plates()->sync($request->get("plate_ids"));
        }

        return jsonResponse(data: [
            "menu" => MenuResource::make($menu)
        ], message: "OK");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant, Menu $menu)
    {
        Menu::where('slug', $menu->slug)->where('restaurant_id', $restaurant->id)->firstOrFail();

        Gate::authorize("delete", $restaurant);

        $menu->delete();

        return jsonResponse(message: "OK");

    }
}
