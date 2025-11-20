<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Vista login
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->role . '.dashboard');
        }


        return view('auth.login');
    }

    // Proceso login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Intento de login
        if (!Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'Credenciales incorrectas']);
        }

        // Usuario autenticado
        $user = Auth::user();

        // Verificar si está activo
        if (!$user->active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Este usuario está inactivo. Contacte al administrador.']);
        }

        // Redirección por rol
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'tecnico') {
            return redirect()->route('tecnico.dashboard');
        }

        return redirect()->route('cliente.dashboard');
    }


    // Vista registro
    public function showRegister()
    {
        return view('auth.register');
    }

    // Proceso registro (solo clientes)
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'unique:users,email'],
            'password' => ['required', 'confirmed']
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'cliente' // fijo
        ]);

        return redirect()->route('login')->with('success', 'Cuenta creada con éxito');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}