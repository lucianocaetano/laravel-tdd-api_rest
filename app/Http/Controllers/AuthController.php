<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordChangeRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    /**
     *  @OA\Post(
     *      path="/api/v1/auth/login",
     *      summary="Login",
     *      tags={"Auth"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property(property="email", type="string", format="email", example="admin@gmail.com"),
     *              @OA\Property(property="password", type="string", format="password", example="admin")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *             @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="token",
     *                      type="string",
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *             @OA\Property(
     *                  property="errors",
     *                  type="array",
     *                  @OA\Items(
     *                      type="string",
     *                      default="incorrect credentials"
     *                  )
     *              )
     *          )
     *      )
     *  )
     *
     */
    public function index(LoginRequest $request)
    {
        $credentials = $request->validated();

        $token = Auth::attempt($credentials);

        if (!$token) {
            return jsonResponse(errors: ["incorrect password"], status: 400);
        }

        return jsonResponse(data: ["token" => $token]);
    }

    /**
     *  @OA\Post(
     *      path="/api/v1/auth/register",
     *      summary="Login",
     *      tags={"Auth"},
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"email", "password", "name"},
     *              @OA\Property(
     *                  property="email",
     *                  default="admin2@gmail.com",
     *                  type="string",
     *                  format="email"
     *              ),
     *              @OA\Property(
     *                  property="password",
     *                  default="admin",
     *                  type="string",
     *                  format="password"
     *              ),
     *              @OA\Property(
     *                  property="name",
     *                  default="admin2",
     *                  type="string",
     *                  format="name"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *             @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="token",
     *                      type="string",
     *                  )
     *              )
     *          )
     *      )
     *  )
     */
    public function store(RegisterRequest $request) {
        $data = $request->validated();

        DB::beginTransaction();
        try{
            $user = User::create($data);
            $user->assignRole(Roles::USER->value);

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
            return jsonResponse(message: 'Internal Server Error', status: 500);
        }

        $token = Auth::login($user);

        return jsonResponse(data: ["token" => $token], status: 201);
    }

    /**
     *  @OA\Post(
     *      path="/api/v1/logout",
     *      summary="Logout",
     *      tags={"Auth"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *             @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="OK"
     *              )
     *          )
     *      )
     *  )
     */
    public function logout(){
        Auth::logout();

        return jsonResponse(message: 'OK');
    }

    /**
     *  @OA\Post(
     *      path="/api/v1/change_password",
     *      summary="Change Password",
     *      tags={"Auth"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *             @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="OK"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *             @OA\Property(
     *                  property="errors",
     *                  type="array",
     *                  @OA\Items(
     *                      type="string",
     *                      default="incorrect credentials"
     *                  )
     *              )
     *          )
     *      ),
     *  )
     */
    public function change_password(PasswordChangeRequest $request){

        $data = $request->validated();
        $user = $request->user();

        if(Hash::check($data["old_password"], $user->password)){
            $user = $user->update([
                "password" => $data["password"],
            ]);

            return jsonResponse(message: "OK");
        }

        return jsonResponse(errors: ["incorrect credentials"], status: 400);
    }

    public function me(Request $request){

        $user = $request->user();
        return jsonResponse(data: ["user" => UserResource::make($user)], message: "OK");
    }
}
