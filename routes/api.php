<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TreeCareController;
use App\Http\Controllers\Api\V1\TreeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function (): void {

        Route::post('register', [AuthController::class, 'register'])
            ->middleware('throttle:5,1')
            ->name('register');

        Route::post('login', [AuthController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('login');

        Route::middleware('auth:sanctum')->group(function (): void {

            Route::post('logout', [AuthController::class, 'logout'])
                ->name('logout');

            Route::post('me', [AuthController::class, 'me'])
                ->name('me');
            // POST, GET Y SHOW de los arboles.
            Route::get('trees', [TreeController::class, 'index'])
                ->name('trees.index');

            Route::post('trees', [TreeController::class, 'store'])
                ->name('trees.store');

            Route::get('trees/{tree}', [TreeController::class, 'show'])
                ->name('trees.show');

            Route::post('trees/{tree}/care', [TreeCareController::class, 'care'])
                ->name('trees.care');
        });

    });
