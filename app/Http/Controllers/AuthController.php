<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordChangeRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index(LoginRequest $request)
    {
        $credentials = $request->validated();

        $token = Auth::attempt($credentials);

        if (!$token) {
            return jsonResponse(errors: ["incorrect credentials"], status: 401);
        }

        return jsonResponse(data: ["token" => $token]);
    }

    public function store (RegisterRequest $request) {
        $data = $request->validated();

        $user = User::create($data);
        $token = Auth::login($user);

        return jsonResponse(data: ["token" => $token], status: 201);
    }

    public function logout(){
        Auth::logout();

        return jsonResponse(message: 'OK');
    }

    public function change_password(PasswordChangeRequest $request){

        $data = $request->validated();
        $user = $request->user();

        if(Hash::check($data["old_password"], $user->password)){
            $user = $user->update([
                "password" => $data["password"],
            ]);

            return jsonResponse(message: "OK");
        }

        return jsonResponse(errors: ["incorrect credentials"], status: 401);
    }

    public function me(Request $request){

        $user = $request->user();
        return jsonResponse(data: ["user" => $user], message: "OK");
    }
}
