<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Api\V1\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function():void{

    Route::post('register', [AuthController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('register');

    Route::post('login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login');


    Route::middleware('auth:sanctum')->group(function():void{

        Route::post('logout', [AuthController::class, 'logout'])
        ->name('logout');

        Route::post('me', [AuthController::class, 'me'])
        ->name('me');

    });

});
