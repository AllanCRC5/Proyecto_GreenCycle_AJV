<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        //
        $user=User::create($request->validated());

        $token= $user->createToken('api')->plainTextToken;

        return (new UserResource($user))
        ->additional(['token'=>$token])
        ->response()
        ->setStatusCode(Response::HTTP_CREATED);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        //
        $user= $request->authenticate();

        $token=$user->createToken('api')->plainTextToken;

        return (new UserResource($user))->additional(['token'=>$token])->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function logout(Request $request)
    {
        //
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "message"=>"Logout successfully",
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function me(Request $request): UserResource
    {
        //
        return new UserResource($request->user());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
