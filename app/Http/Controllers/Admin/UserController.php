<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * LISTA DE USUARIOS
     */
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * FORMULARIO PARA CREAR USUARIO
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * GUARDAR USUARIO
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:admin,tecnico,cliente',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'active'   => true,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * FORMULARIO PARA EDITAR USUARIO
     */
    public function edit(User $user)
    {
        // Seguridad: evitar editar otro admin si eres tú mismo
        if (Auth::id() === $user->id) {
            return back()->with('error', 'No puedes editarte a ti mismo desde aquí.');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * ACTUALIZAR USUARIO
     */
    public function update(Request $request, User $user)
    {
        // El admin no se puede editar a sí mismo aquí
        if (Auth::id() === $user->id) {
            return back()->with('error', 'No puedes editar tu propio usuario desde aquí.');
        }

        // Validaciones
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => "required|email|unique:users,email,$user->id",
            'role'  => 'required|in:admin,tecnico,cliente',
        ]);

        // Si envía contraseña nueva, validar
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:6|confirmed'
            ]);

            $user->password = Hash::make($request->password);
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->role  = $request->role;

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * DESACTIVAR USUARIO
     */
    public function disable(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'No puedes desactivar a un administrador.');
        }

        $user->active = false;
        $user->save();

        return back()->with('success', 'Usuario desactivado.');
    }

    /**
     * ACTIVAR USUARIO
     */
    public function activate(User $user)
    {
        $user->active = true;
        $user->save();

        return back()->with('success', 'Usuario activado.');
    }

    /**
     * ELIMINAR USUARIO
     */
    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'No puedes eliminar administradores.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario eliminado definitivamente.');
    }
}
