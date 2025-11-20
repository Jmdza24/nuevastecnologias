<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Lista de tickets (según el rol).
     */
    public function index()
    {
        // En Fase 4: Cliente verá sus tickets
        // Técnico verá asignados
        // Admin verá todos
    }

    /**
     * Formulario para crear un ticket (solo cliente).
     */
    public function create()
    {
        return view('tickets.create');
    }

    /**
     * Guardar ticket en DB (solo cliente).
     */
    public function store(Request $request)
    {
        // En Fase 4: Validar y crear ticket
    }

    /**
     * Ver detalle de un ticket.
     */
    public function show(Ticket $ticket)
    {
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Formulario para editar ticket (técnico/admin).
     */
    public function edit(Ticket $ticket)
    {
        return view('tickets.edit', compact('ticket'));
    }

    /**
     * Actualizar ticket (cambiar estado / asignar técnico).
     */
    public function update(Request $request, Ticket $ticket)
    {
        // En Fase 4: Actualizar ticket
    }

    /**
     * Cerrar ticket (solo cliente).
     */
    public function close(Ticket $ticket)
    {
        // En Fase 4: Cerrar ticket
    }

    /**
     * Eliminar ticket (solo admin).
     */
    public function destroy(Ticket $ticket)
    {
        // En Fase 4: Eliminar ticket
    }
}
