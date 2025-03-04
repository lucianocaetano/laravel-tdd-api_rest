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

        $menus = $restaurant->menus()
            ->filter()
            ->sort()
            ->search(field: 'description')
            ->paginate();

        return jsonResponse(data:  new MenuCollection($menus), message: "OK", status: 200);
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

    public function show(Restaurant $restaurant, Menu $menu)
    {
        Gate::authorize("view", $restaurant);
        Gate::authorize("view", $menu);

        return jsonResponse(data: [
            "menu" => MenuResource::make($menu)
        ], message: "OK");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request, Restaurant $restaurant, Menu $menu)
    {
        Gate::authorize("view", $restaurant);
        Gate::authorize("update", $menu);

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
        Gate::authorize("view", $restaurant);
        Gate::authorize("delete", $menu);

        $menu->delete();

        return jsonResponse(message: "OK");

    }
}
