<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('dashboard.admin', [

            // Estadísticas de tickets
            'total'            => Ticket::count(),
            'open'             => Ticket::where('status', 'open')->count(),
            'in_progress'      => Ticket::where('status', 'in_progress')->count(),
            'waiting_client'   => Ticket::where('status', 'waiting_client')->count(),
            'finished'         => Ticket::where('status', 'finished')->count(),
            'closed'           => Ticket::where('status', 'closed')->count(),

            // Últimos 10 tickets
            'tickets' => Ticket::with('creator', 'assignedTo')
                    ->latest()
                    ->take(10)
                    ->get(),


            // Tickets por técnico
            'ticketsPorTecnico' => User::where('role', 'tecnico')
                ->withCount('ticketsAssigned')
                ->get(),

            // Lista de usuarios (para tu tabla)
            'users' => User::orderBy('role')->get(),
        ]);
    }


    public function tecnico()
    {
        $user = Auth::user();

        return view('dashboard.tecnico', [
            'asignados'      => Ticket::where('assigned_to', $user->id)->count(),
            'en_proceso'     => Ticket::where('assigned_to', $user->id)->where('status', 'in_progress')->count(),
            'terminados'     => Ticket::where('assigned_to', $user->id)->where('status', 'finished')->count(),
            'espera_cliente' => Ticket::where('assigned_to', $user->id)->where('status', 'waiting_client')->count(),

            'misTickets'     => Ticket::where('assigned_to', $user->id)->latest()->take(5)->get(),
        ]);
    }


    public function cliente()
    {
        $user = Auth::user();

        return view('dashboard.cliente', [
            'total'        => Ticket::where('created_by', $user->id)->count(),
            'abiertos'     => Ticket::where('created_by', $user->id)->where('status', 'open')->count(),
            'esperando'    => Ticket::where('created_by', $user->id)->where('status', 'waiting_client')->count(),
            'resueltos'    => Ticket::where('created_by', $user->id)->where('status', 'finished')->count(),
            'cerrados'     => Ticket::where('created_by', $user->id)->where('status', 'closed')->count(),

            'misTickets'   => Ticket::where('created_by', $user->id)->latest()->take(5)->get(),
        ]);
    }
}
