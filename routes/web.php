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

    // Ver todos los tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');

    // Ver detalle
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    // Asignar o editar ticket
    Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');

    // Eliminar ticket
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
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

    // Ver tickets asignados o disponibles
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');

    // Ver detalle
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    // Editar ticket (cambiar estado)
    Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
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

    // Listar mis tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');

    // Crear ticket
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');

    // Ver detalle
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    // Cerrar ticket
    Route::post('/tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');
});
