@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="container mt-4">

    <h2 class="fw-bold mb-4"> Editar Usuario</h2>

    <div class="card shadow-sm">
        <div class="card-body">

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Rol</label>
                    <select name="role" class="form-select" required>
                        <option value="cliente" {{ $user->role=='cliente' ? 'selected' : '' }}>Cliente</option>
                        <option value="tecnico" {{ $user->role=='tecnico' ? 'selected' : '' }}>Técnico</option>
                        <option value="admin" {{ $user->role=='admin' ? 'selected' : '' }}>Administrador</option>
                    </select>
                </div>

                <hr>

                <h5 class="fw-bold">Cambiar contraseña (opcional)</h5>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nueva contraseña</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <button class="btn btn-primary">
                    <i class="bi bi-save"></i> Actualizar
                </button>

                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>

            </form>

        </div>
    </div>

</div>
@endsection
