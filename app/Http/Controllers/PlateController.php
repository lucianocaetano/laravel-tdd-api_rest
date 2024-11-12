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
        Gate::authorize("view", $restaurant);
        $plates = $restaurant->plates()->paginate();

        return jsonResponse(message: "OK", data:  new PlateCollection($plates));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Restaurant $restaurant, StorePlateRequest $request)
    {
        Gate::authorize("view", $restaurant);
        $data = $request->validated();

        $plates = $restaurant->plates()->create($data);

        //$data['image'] = $this->helper->uploadImage($request->get('image'), $restaurant->id);

        return jsonResponse(message: "OK", data:  PlateResource::make($plates));
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant, Plate $plate)
    {
        Gate::authorize("view", $restaurant);
        Gate::authorize("view", $plate);

        return jsonResponse(message: "OK", data: [
            "plate" => PlateResource::make($plate)
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Restaurant $restaurant, Plate $plate, UpdatePlateRequest $request)
    {
        Gate::authorize("view", $restaurant);
        Gate::authorize("update", $plate);

        $data = $request->validated();

        if($request->get('image')){
            //$data['image'] = $this->helper->uploadImage($request->get('image'), $restaurant->id);
        }

        $plate->update($data);

        return jsonResponse(message: "OK", data:  [ "plate" => PlateResource::make($plate) ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant, Plate $plate)
    {
        Gate::authorize("view", $restaurant);
        Gate::authorize("delete", $plate);

        $plate->delete();

        return jsonResponse(message: "OK");
    }
}
