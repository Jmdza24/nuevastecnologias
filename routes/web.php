<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS (INVITADOS)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    if (Auth::check()) {

        return match (Auth::user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'tecnico' => redirect()->route('tecnico.dashboard'),
            default   => redirect()->route('cliente.dashboard'),
        };
    }

    return redirect()->route('login');
});

// LOGIN/Register solo para invitados
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// LOGOUT
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| ---------------------- TICKETS (TODOS LOS ROLES TIENEN ACCESO) ----------------------
|--------------------------------------------------------------------------
|
|  - Admin ve todos
|  - Técnico ve los asignados
|  - Cliente ve los suyos
|  El filtrado NO se hace aquí → se hace en TicketController@index
|
*/

Route::middleware('auth')->group(function () {

    // LISTADO DE TICKETS (todos los roles)
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');

    // CREAR (solo cliente)
    Route::middleware('role:cliente')->group(function () {
        Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
        Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
        Route::post('/tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');
    });

    // DETALLE (roles limitados desde controller)
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    // EDITAR / ACTUALIZAR (admin + técnico)
    Route::middleware('role:admin,tecnico')->group(function () {
        Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
        Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    });

    // TOMAR (solo técnico)
    Route::post('/tickets/{ticket}/take', [TicketController::class, 'take'])
        ->middleware('role:tecnico')
        ->name('tickets.take');

    // ELIMINAR (solo admin)
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('tickets.destroy');
});


/*
|--------------------------------------------------------------------------
| ---------------------- ADMIN ----------------------
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->name('admin.dashboard');

    /*
    |----------------------------------------------------------------------
    | Gestión de usuarios
    |----------------------------------------------------------------------
    */

    Route::prefix('admin/users')->name('admin.users.')->group(function () {

        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');

        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');

        Route::put('/{user}/disable', [UserController::class, 'disable'])->name('disable');
        Route::put('/{user}/activate', [UserController::class, 'activate'])->name('activate');

        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
});


/*
|--------------------------------------------------------------------------
| ---------------------- TÉCNICO ----------------------
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:tecnico'])->group(function () {

    Route::get('/dashboard/tecnico', [DashboardController::class, 'tecnico'])
        ->name('tecnico.dashboard');
});


/*
|--------------------------------------------------------------------------
| ---------------------- CLIENTE ----------------------
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:cliente'])->group(function () {

    Route::get('/dashboard/cliente', [DashboardController::class, 'cliente'])
        ->name('cliente.dashboard');
});
