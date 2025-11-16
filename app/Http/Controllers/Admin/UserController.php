<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', User::class);

        $users = User::paginate();

        return jsonResponse(data: new UserCollection($users), message: 'OK');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        Gate::authorize('create', User::class);

        $data = $request->validated();

        $user = User::create($data);

        return jsonResponse(data: $user, message: 'User store successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        Gate::authorize('view', $user);

        return jsonResponse(data: ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        Gate::authorize('update', $user);

        $data = $request->validated();

        $user = $user->update($data);

        return jsonResponse(data: $user, message: 'User update successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        $user = $user->delete();

        return jsonResponse(message: 'User remove successfully');
    }

}
