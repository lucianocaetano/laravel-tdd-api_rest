<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        dd($request->user());
        return jsonResponse(data: ["user" => $request->user()], message: "OK");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request)
    {
        $data = $request->validated();

        if(count($data) === 0){
            return jsonResponse(message: "no field was received", status: 400);
        }

        $user = $request->user();

        $user->update(
            $data
        );

        return jsonResponse(data: ["user" => $user], message: "OK");
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->user()->delete();

        return jsonResponse(message: "OK");
    }
}
