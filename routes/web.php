
<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Para autenticarse
Route::get('/login', function () {
    return view('respuesta-api', [
        'titulo' => 'Iniciar sesión',
        'endpoint' => '/api/v1/login',
        'method' => 'POST',
        'requiresToken' => false,
    ]);
});

Route::get('/register', function () {
    return view('respuesta-api', [
        'titulo' => 'Registrar usuario',
        'endpoint' => '/api/v1/register',
        'method' => 'POST',
        'requiresToken' => false,
    ]);
});

Route::get('/logout', function () {
    return view('respuesta-api', [
        'titulo' => 'Cerrar sesión',
        'endpoint' => '/api/v1/logout',
        'method' => 'POST',
        'requiresToken' => true,
    ]);
});

// Cuando el usuario está autenticado
Route::get('/me', function () {
    return view('respuesta-api', [
        'titulo' => 'Consultar usuario',
        'endpoint' => '/api/v1/me',
        'method' => 'GET',
        'requiresToken' => true,
    ]);
});

// Devuelve todos los árboles
Route::get('/trees', function () {
    return view('respuesta-api', [
        'titulo' => 'Consultar árboles',
        'endpoint' => '/api/v1/tree',
        'method' => 'GET',
        'requiresToken' => true,
    ]);
});

// Crear árbol
Route::get('/trees/create', function () {
    return view('respuesta-api', [
        'titulo' => 'Crear árbol',
        'endpoint' => '/api/v1/tree',
        'method' => 'POST',
        'requiresToken' => true,
    ]);
});

// Busca un árbol por id para cuidarlo
Route::get('/trees/{tree}/care', function ($tree) {
    return view('respuesta-api', [
        'titulo' => 'Cuidar árbol',
        'endpoint' => "/api/v1/tree/{$tree}/care",
        'method' => 'POST',
        'requiresToken' => true,
    ]);
});

// busca un árbol
Route::get('/trees/{tree}', function ($tree) {
    return view('respuesta-api', [
        'titulo' => 'Consultar árbol',
        'endpoint' => "/api/v1/tree/{$tree}",
        'method' => 'GET',
        'requiresToken' => true,
    ]);
});
