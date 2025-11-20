@extends('layouts.app')

@section('title', 'Crear Usuario')

@section('content')
<div class="container mt-4">

    <h2 class="fw-bold mb-4"> Crear Usuario</h2>

    <div class="card shadow-sm">
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Rol</label>
                    <select name="role" class="form-select" required>
                        <option value="cliente">Cliente</option>
                        <option value="tecnico">Técnico</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <button class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar
                </button>

                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>

            </form>
        </div>
    </div>

</div>
@endsection
