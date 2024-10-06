<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlateRequest;
use App\Http\Requests\UpdatePlateRequest;
use App\Http\Resources\PlateCollection;
use App\Http\Resources\PlateResource;
use App\Models\Plate;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Gate;

class PlateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Restaurant $restaurant)
    {
        $plates = $restaurant->plates()->paginate();

        return jsonResponse(message: "OK", data:  new PlateCollection($plates));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Restaurant $restaurant, StorePlateRequest $request)
    {
        $data = $request->validated();
        $plates = $restaurant->plates()->create($data);

        return jsonResponse(message: "OK", data:  PlateResource::make($plates));
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant, string $plate_slug)
    {
        $plate = Plate::where('slug', $plate_slug)->where('restaurant_id', $restaurant->id)->firstOrFail();

        Gate::authorize("view", $plate);

        return jsonResponse(message: "OK", data: [
            "plate" => PlateResource::make($plate)
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Restaurant $restaurant, string $plate_slug, UpdatePlateRequest $request)
    {
        $plate = Plate::where('slug', $plate_slug)->where('restaurant_id', $restaurant->id)->firstOrFail();

        Gate::authorize("update", $plate);

        $data = $request->validated();

        $plate->update($data);

        return jsonResponse(message: "OK", data:  [ "restaurant" => PlateResource::make($plate) ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant, string $plate_slug)
    {
        $plate = Plate::where('slug', $plate_slug)->where('restaurant_id', $restaurant->id)->firstOrFail();

        Gate::authorize("delete", $plate);

        $plate->delete();

        return jsonResponse(message: "OK");
    }
}
