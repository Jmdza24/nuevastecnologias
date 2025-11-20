<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Rutas públicas (sin autenticación)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Register (solo clientes)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

/*
|--------------------------------------------------------------------------
| Logout (solo usuarios autenticados)
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Dashboard ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard/admin', function () {
        return view('dashboard.admin');
    })->name('admin.dashboard');

    // Rutas futuras del administrador...
});

/*
|--------------------------------------------------------------------------
| Dashboard TÉCNICO
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:tecnico'])->group(function () {
    Route::get('/dashboard/tecnico', function () {
        return view('dashboard.tecnico');
    })->name('tecnico.dashboard');

    // Rutas futuras del técnico...
});

/*
|--------------------------------------------------------------------------
| Dashboard CLIENTE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:cliente'])->group(function () {
    Route::get('/dashboard/cliente', function () {
        return view('dashboard.cliente');
    })->name('cliente.dashboard');

    // Rutas futuras del cliente...
});
