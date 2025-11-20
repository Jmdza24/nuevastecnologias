@extends('layouts.app')

@section('title', 'Panel Cliente')

@section('content')
<div class="container mt-4">

    <h2 class="fw-bold mb-4">🙋 Panel del Cliente</h2>

    <div class="row g-3">
        <x-dashboard-card title="Mis tickets" :value="$total" color="primary"/>
        <x-dashboard-card title="Abiertos" :value="$abiertos" color="success"/>
        <x-dashboard-card title="Esperando respuesta" :value="$esperando" color="warning"/>
        <x-dashboard-card title="Resueltos" :value="$resueltos" color="info"/>
        <x-dashboard-card title="Cerrados" :value="$cerrados" color="secondary"/>
    </div>

    <hr class="my-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">📌 Mis tickets recientes</h4>
        <div class="d-flex align-items-center">
            <a href="{{ route('tickets.index') }}" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left"></i> Ver Listado
            </a>
            <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Crear Ticket
            </a>
        </div>
    </div>

    <ul class="list-group">
        @foreach($misTickets as $t)
            <li class="list-group-item d-flex justify-content-between">
                <span>#{{ $t->id }} — {{ $t->subject }}</span>
                <a href="{{ route('tickets.show', $t) }}" class="btn btn-sm btn-outline-primary">Ver</a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
