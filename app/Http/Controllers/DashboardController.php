<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard ADMIN
     */
    public function admin()
    {
        return view('dashboard.admin', [
            'total'            => Ticket::count(),
            'open'             => Ticket::where('status', 'open')->count(),
            'in_progress'      => Ticket::where('status', 'in_progress')->count(),
            'waiting_client'   => Ticket::where('status', 'waiting_client')->count(),
            'finished'         => Ticket::where('status', 'finished')->count(),
            'closed'           => Ticket::where('status', 'closed')->count(),

            // Tickets por técnico
            'ticketsPorTecnico' => \App\Models\User::where('role', 'tecnico')
                ->withCount('ticketsAssigned')
                ->get(),
        ]);
    }

    /**
     * Dashboard TÉCNICO
     */
    public function tecnico()
    {
        $user = Auth::user();

        return view('dashboard.tecnico', [
            'asignados'        => Ticket::where('assigned_to', $user->id)->count(),
            'en_proceso'       => Ticket::where('assigned_to', $user->id)
                                        ->where('status', 'in_progress')->count(),
            'terminados'       => Ticket::where('assigned_to', $user->id)
                                        ->where('status', 'finished')->count(),
            'espera_cliente'   => Ticket::where('assigned_to', $user->id)
                                        ->where('status', 'waiting_client')->count(),

            // Lista rápida
            'misTickets'       => Ticket::where('assigned_to', $user->id)
                                        ->latest()->take(5)->get(),
        ]);
    }

    /**
     * Dashboard CLIENTE
     */
    public function cliente()
    {
        $user = Auth::user();

        return view('dashboard.cliente', [
            'total'            => Ticket::where('created_by', $user->id)->count(),
            'abiertos'         => Ticket::where('created_by', $user->id)->where('status', 'open')->count(),
            'esperando'        => Ticket::where('created_by', $user->id)->where('status', 'waiting_client')->count(),
            'resueltos'        => Ticket::where('created_by', $user->id)->where('status', 'finished')->count(),
            'cerrados'         => Ticket::where('created_by', $user->id)->where('status', 'closed')->count(),

            // Lista rápida
            'misTickets'       => Ticket::where('created_by', $user->id)
                                        ->latest()->take(5)->get(),
        ]);
    }
}
