<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /********************************************************************
     * REGISTRO DE LOGS
     ********************************************************************/
    private function addLog(Ticket $ticket, string $action, ?string $description = null)
    {
        \App\Models\TicketLog::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => Auth::id(),
            'action'      => $action,
            'description' => $description,
        ]);
    }

    /********************************************************************
     * FORMATEO DE LOGS (español + nombres)
     ********************************************************************/
    private function formatLog($log)
    {
        // Diccionario de estados en español
        $labels = [
            'open'            => 'Abierto',
            'in_progress'     => 'En progreso',
            'waiting_client'  => 'Esperando respuesta del cliente',
            'finished'        => 'Terminado',
            'closed'          => 'Cerrado',
        ];

        // Traducción de acciones
        $action = match ($log->action) {
            'ticket creado'     => 'Ticket creado',
            'ticket eliminado'  => 'Ticket eliminado',
            'ticket cerrado'    => 'Ticket cerrado',
            'estado cambiado'   => 'Cambio de estado',
            'técnico asignado'  => 'Técnico asignado',
            default             => ucfirst($log->action),
        };

        // Descripción formateada
        $description = $log->description;

        // Reemplazar estados a español
        foreach ($labels as $key => $value) {
            $description = str_replace($key, $value, $description);
        }

        // Reemplazar “usuario ID X” por nombre real
        if (preg_match('/usuario ID (\d+)/', $description, $match)) {
            $user = \App\Models\User::find($match[1]);
            if ($user) {
                $description = str_replace(
                    "usuario ID {$match[1]}",
                    "{$user->name} (técnico)",
                    $description
                );
            }
        }

        return [
            'action' => $action,
            'description' => $description,
            'user' => $log->user->name,
            'date' => $log->created_at->format('d/m/Y H:i'),
        ];
    }

    /********************************************************************
     * LISTADO DE TICKETS SEGÚN ROL
     ********************************************************************/
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::query();

        if ($user->role === 'cliente') {
            $query->where('created_by', $user->id);
        }

        if ($user->role === 'tecnico') {
            $query->where('assigned_to', $user->id);
        }

        if ($request->filled('buscar')) {
            $query->where('subject', 'like', "%{$request->buscar}%");
        }

        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('status', $request->estado);
        }

        if ($request->filled('fecha_inicial')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicial);
        }

        if ($request->filled('fecha_final')) {
            $query->whereDate('created_at', '<=', $request->fecha_final);
        }

        if ($user->role === 'admin') {
            if ($request->filled('cliente_id')) {
                $query->where('created_by', $request->cliente_id);
            }
            if ($request->filled('tecnico_id')) {
                $query->where('assigned_to', $request->tecnico_id);
            }
        }

        $tickets = $query->latest()->get();

        $clientes = $user->role === 'admin'
            ? \App\Models\User::where('role', 'cliente')->get()
            : null;

        $tecnicos = $user->role === 'admin'
            ? \App\Models\User::where('role', 'tecnico')->get()
            : null;

        return view('tickets.index', compact('tickets', 'user', 'clientes', 'tecnicos'));
    }

    /********************************************************************
     * CREAR TICKET (CLIENTE)
     ********************************************************************/
    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string|min:10',
        ]);

        $ticket = Ticket::create([
            'subject'     => $request->subject,
            'description' => $request->description,
            'status'      => 'open',
            'created_by'  => Auth::id(),
            'assigned_to' => null,
            'closed_at'   => null,
        ]);

        $this->addLog($ticket, 'ticket creado', 'El cliente creó el ticket.');

        return redirect()->route('tickets.index')
            ->with('success', 'El ticket fue creado correctamente.');
    }

    /********************************************************************
     * VER TICKET
     ********************************************************************/
    public function show(Ticket $ticket)
    {
        $user = Auth::user();

        if ($user->role === 'cliente' && $ticket->created_by !== $user->id) abort(403);
        if ($user->role === 'tecnico' && $ticket->assigned_to !== $user->id) abort(403);

        // Formatear historial
        $logs = $ticket->logs->map(fn($l) => $this->formatLog($l));

        // Estado en español
        $labels = [
            'open'            => 'Abierto',
            'in_progress'     => 'En progreso',
            'waiting_client'  => 'Esperando respuesta del cliente',
            'finished'        => 'Terminado',
            'closed'          => 'Cerrado',
        ];

        $colors = [
            'open'            => 'primary',
            'in_progress'     => 'info',
            'waiting_client'  => 'warning',
            'finished'        => 'secondary',
            'closed'          => 'dark',
        ];

        return view('tickets.show', compact('ticket', 'user', 'labels', 'colors', 'logs'));
    }

    /********************************************************************
     * EDITAR TICKET
     ********************************************************************/
    public function edit(Ticket $ticket)
    {
        $user = Auth::user();

        // Bloquear si está terminado o cerrado
        if (in_array($ticket->status, ['finished', 'closed'])) {
            abort(403, 'Este ticket ya está finalizado y no puede modificarse.');
        }

        // CLIENTE → no puede editar nada
        if ($user->role === 'cliente') {
            abort(403, 'No tienes permiso para editar este ticket.');
        }

        // TÉCNICO → solo tickets asignados
        if ($user->role === 'tecnico' && $ticket->assigned_to !== $user->id) {
            abort(403, 'No puedes editar este ticket.');
        }

        $technicians = $user->role === 'admin'
            ? \App\Models\User::where('role', 'tecnico')->get()
            : null;

        return view('tickets.edit', compact('ticket', 'user', 'technicians'));
    }


    /********************************************************************
     * ACTUALIZAR TICKET
     ********************************************************************/
    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        if (in_array($ticket->status, ['finished', 'closed'])) {
            abort(403, 'Este ticket ya está finalizado y no puede modificarse.');
        }

        if ($user->role === 'cliente') abort(403);
        if ($user->role === 'tecnico' && $ticket->assigned_to !== $user->id) abort(403);

        $rules = [
            'status' => 'required|in:open,in_progress,waiting_client,finished,closed',
        ];

        if ($user->role === 'admin') {
            $rules['assigned_to'] = 'nullable|exists:users,id';
        }

        $data = $request->validate($rules);

        // ASIGNACIÓN DEL TÉCNICO
        if ($user->role === 'admin') {

            if ($ticket->assigned_to != ($data['assigned_to'] ?? null)) {

                // Verificar que sea técnico
                if (!empty($data['assigned_to'])) {
                    $tecnico = \App\Models\User::find($data['assigned_to']);
                    if (!$tecnico || $tecnico->role !== 'tecnico') {
                        abort(403, 'Solo se puede asignar técnicos.');
                    }
                }

                $this->addLog(
                    $ticket,
                    'técnico asignado',
                    'Asignado a usuario ID ' . ($data['assigned_to'] ?? 'Ninguno')
                );
            }

            $ticket->assigned_to = $data['assigned_to'] ?? null;
        }

        // TÉCNICO NO CIERRA TICKETS
        if ($user->role === 'tecnico' && $data['status'] === 'closed') {
            abort(403, 'El técnico no puede cerrar tickets.');
        }

        // CAMBIO DE ESTADO
        if ($ticket->status !== $data['status']) {
            $this->addLog(
                $ticket,
                'estado cambiado',
                'Estado cambiado de ' . $ticket->status . ' a ' . $data['status']
            );
        }

        $ticket->status = $data['status'];

        if ($ticket->status === 'closed') {
            $ticket->closed_at = now();
        }

        $ticket->save();

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'El ticket fue actualizado correctamente.');
    }

    /********************************************************************
     * CERRAR TICKET (CLIENTE)
     ********************************************************************/
    public function close(Ticket $ticket)
    {
        $user = Auth::user();

        if ($user->role !== 'cliente') abort(403);
        if ($ticket->created_by !== $user->id) abort(403);

        $ticket->status = 'closed';
        $ticket->closed_at = now();
        $ticket->save();

        $this->addLog($ticket, 'ticket cerrado', 'El cliente cerró el ticket.');

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket cerrado exitosamente.');
    }

    /********************************************************************
     * ELIMINAR TICKET (ADMIN)
     ********************************************************************/
    public function destroy(Ticket $ticket)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $this->addLog($ticket, 'ticket eliminado', 'Ticket eliminado por administrador.');

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'El ticket fue eliminado correctamente.');
    }

    /********************************************************************
     * TOMAR TICKET (TÉCNICO)
     ********************************************************************/
    public function take(Ticket $ticket)
    {
        $user = Auth::user();

        if ($user->role !== 'tecnico') abort(403);

        if ($ticket->assigned_to !== null) {
            return redirect()->route('tickets.index')
                ->with('success', 'Este ticket ya está asignado.');
        }

        $ticket->assigned_to = $user->id;
        $ticket->status = 'in_progress';
        $ticket->save();

        $this->addLog($ticket, 'ticket tomado', 'El técnico tomó el ticket.');

        return redirect()->route('tickets.index')
            ->with('success', 'Has tomado el ticket exitosamente.');
    }
}
