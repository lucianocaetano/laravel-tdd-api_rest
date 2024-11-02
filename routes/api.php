<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PlateController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(["guest"])->prefix("/auth")->group(function () {
    Route::post("/login", [AuthController::class, "index"])->name("login");
    Route::post("/register", [AuthController::class, "store"])->name("register");
})->name("auth.");

Route::middleware(["auth"])->group(function () {
    Route::post("/logout", [AuthController::class, "logout"])->name("logout");
    Route::patch("/change_password", [AuthController::class, "change_password"])->name("change-password");

    Route::prefix("/user")->group(function () {
        Route::patch("/update", [UserController::class, "update"])->name("update");
        Route::delete("/remove", [UserController::class, "destroy"])->name("remove");
        Route::get("/me", [AuthController::class, "me"])->name("me");
    })->name("user.");

    // api restaurant
    Route::apiResource("/restaurant", RestaurantController::class);
    Route::apiResource("/{restaurant:slug}/plate", PlateController::class);
    Route::apiResource("/{restaurant:slug}/menu", MenuController::class);
});


