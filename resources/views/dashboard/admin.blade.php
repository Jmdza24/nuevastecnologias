@extends('layouts.app')

@section('title', 'Panel Administrador')

@section('content')
<div class="container mt-4">

    <h2 class="fw-bold mb-4">📊 Panel de Administración</h2>

    <div class="row g-3">

        <x-dashboard-card title="Total Tickets" :value="$total" color="primary" />
        <x-dashboard-card title="Abiertos" :value="$open" color="success" />
        <x-dashboard-card title="En proceso" :value="$in_progress" color="info" />
        <x-dashboard-card title="Esperando cliente" :value="$waiting_client" color="warning" />
        <x-dashboard-card title="Terminados" :value="$finished" color="secondary" />
        <x-dashboard-card title="Cerrados" :value="$closed" color="dark" />

    </div>

    <hr class="my-4">

    <h4 class="fw-bold">👷‍♂️ Tickets por técnico</h4>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Técnico</th>
                <th>Email</th>
                <th>Tickets asignados</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ticketsPorTecnico as $tec)
                <tr>
                    <td>{{ $tec->name }}</td>
                    <td>{{ $tec->email }}</td>
                    <td>{{ $tec->tickets_assigned_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
