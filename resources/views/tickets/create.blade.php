@extends('layouts.app')

@section('title', 'Crear Ticket')

@section('content')
<div class="container mt-5">

    <h2 class="mb-4">Crear nuevo ticket</h2>

    <!-- Mensajes de éxito -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Errores -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="m-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('tickets.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Asunto</label>
                    <input type="text" name="subject" 
                           class="form-control" 
                           value="{{ old('subject') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" rows="4" 
                              class="form-control"
                              required>{{ old('description') }}</textarea>
                </div>

                <button class="btn btn-primary">Crear Ticket</button>
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>

            </form>

        </div>
    </div>

</div>
@endsection
