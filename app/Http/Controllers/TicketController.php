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
        $user = Auth::user();

        // CLIENTE → solo sus tickets
        if ($user->role === 'cliente') {
            $tickets = Ticket::where('created_by', $user->id)->get();
        }

        // TÉCNICO → tickets asignados a él
        else if ($user->role === 'tecnico') {
            $tickets = Ticket::where('assigned_to', $user->id)->get();
        }

        // ADMIN → todos los tickets
        else {
            $tickets = Ticket::all();
        }

        return view('tickets.index', compact('tickets'));
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
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|min:10',
        ]);

        Ticket::create([
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'open',                  // por defecto
            'created_by' => Auth::id(),          // cliente autenticado
            'assigned_to' => null,               // sin técnico asignado
            'closed_at' => null,
        ]);

        return redirect()
            ->route('tickets.index')
            ->with('success', 'El ticket fue creado correctamente.');
    }

    /**
     * Ver detalle de un ticket.
     */
    public function show(Ticket $ticket)
    {
        $user = Auth::user();

        // Restricción de acceso:
        // CLIENTE → solo puede ver sus propios tickets
        if ($user->role === 'cliente' && $ticket->created_by !== $user->id) {
            abort(403, 'No puedes ver este ticket.');
        }

        // TÉCNICO → solo puede ver tickets asignados a él
        if ($user->role === 'tecnico' && $ticket->assigned_to !== $user->id) {
            abort(403, 'No puedes ver este ticket.');
        }

        // ADMIN → puede ver cualquiera (sin restricción)

        return view('tickets.show', compact('ticket', 'user'));
    }

    /**
     * Formulario para editar ticket (técnico/admin).
     */
    public function edit(Ticket $ticket)
    {
        $user = Auth::user();

        // CLIENTE NO PUEDE EDITAR NADA
        if ($user->role === 'cliente') {
            abort(403, 'No tienes permiso para editar este ticket.');
        }

        // TÉCNICO --> solo puede editar tickets asignados a él
        if ($user->role === 'tecnico' && $ticket->assigned_to !== $user->id) {
            abort(403, 'No puedes editar este ticket.');
        }

        // ADMIN --> verá lista de técnicos para asignar ticket
        $technicians = null;

        if ($user->role === 'admin') {
            $technicians = \App\Models\User::where('role', 'tecnico')->get();
        }

        return view('tickets.edit', [
            'ticket' => $ticket,
            'user' => $user,
            'technicians' => $technicians
        ]);
    }


    /**
     * Actualizar ticket (cambiar estado / asignar técnico).
     */
    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        // CLIENTE NO EDITA
        if ($user->role === 'cliente') {
            abort(403, 'No tienes permiso para actualizar este ticket.');
        }

        // TÉCNICO --> solo tickets asignados
        if ($user->role === 'tecnico' && $ticket->assigned_to !== $user->id) {
            abort(403, 'No puedes actualizar este ticket.');
        }

        // Validaciones según rol
        $rules = [
            'status' => 'required|in:open,in_progress,waiting_client,finished,closed',
        ];

        // ADMIN puede asignar técnico
        if ($user->role === 'admin') {
            $rules['assigned_to'] = 'nullable|exists:users,id';
        }

        $data = $request->validate($rules);

        // ADMIN: puede asignar técnico
        if ($user->role === 'admin') {
            $ticket->assigned_to = $data['assigned_to'] ?? null;
        }

        // TÉCNICO: NO puede cerrar el ticket
        if ($user->role === 'tecnico' && $data['status'] === 'closed') {
            abort(403, 'El técnico no puede cerrar tickets.');
        }

        // Actualizar estado
        $ticket->status = $data['status'];

        // Si alguien lo marca como "closed" → guardamos fecha
        if ($ticket->status === 'closed') {
            $ticket->closed_at = now();
        }

        $ticket->save();

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'El ticket fue actualizado correctamente.');
    }

    /**
     * Cerrar ticket (solo cliente).
     */
    public function close(Ticket $ticket)
    {
        $user = Auth::user();

        // Solo el cliente puede cerrar un ticket
        if ($user->role !== 'cliente') {
            abort(403, 'Solo el cliente puede cerrar tickets.');
        }

        // Cliente solo puede cerrar SUS propios tickets
        if ($ticket->created_by !== $user->id) {
            abort(403, 'No puedes cerrar este ticket.');
        }

        // Solo se pueden cerrar tickets que no estén ya cerrados
        if ($ticket->status === 'closed') {
            return redirect()
                ->route('tickets.show', $ticket)
                ->with('success', 'Este ticket ya está cerrado.');
        }

        // Cerrar ticket
        $ticket->status = 'closed';
        $ticket->closed_at = now();
        $ticket->save();

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket cerrado exitosamente.');
    }

    /**
     * Eliminar ticket (solo admin).
     */
    public function destroy(Ticket $ticket)
    {
        $user = Auth::user();

        // Solo el ADMIN puede eliminar tickets
        if ($user->role !== 'admin') {
            abort(403, 'No tienes permiso para eliminar tickets.');
        }

        $ticket->delete();

        return redirect()
            ->route('tickets.index')
            ->with('success', 'El ticket fue eliminado correctamente.');
    }
}
