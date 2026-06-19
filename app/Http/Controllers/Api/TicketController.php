<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index()
    {
        return Ticket::with(['patient', 'service', 'cabinet'])
            ->where('deleted', 'NON')
            ->whereDate('date_ticket', now()->toDateString())
            ->latest()
            ->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'service_id' => 'required|exists:services,id',
            'cabinet_id' => 'nullable|exists:cabinets,id',
            'priorite' => 'nullable|integer|min:0',
        ]);

        return DB::transaction(function () use ($data) {

            $service = Service::findOrFail($data['service_id']);

            $date = now()->toDateString();

            $codeService = $service->code_service ?: 'TCK';

            $dernierNumero = Ticket::where('service_id', $service->id)
                ->whereDate('date_ticket', $date)
                ->lockForUpdate()
                ->count() + 1;

            $numeroTicket = $codeService . '-' . str_pad($dernierNumero, 3, '0', STR_PAD_LEFT);

            $ticket = Ticket::create([
                'patient_id' => $data['patient_id'] ?? null,
                'service_id' => $data['service_id'],
                'cabinet_id' => $data['cabinet_id'] ?? null,
                'numero_ticket' => $numeroTicket,
                'date_ticket' => $date,
                'statut' => 'EN_ATTENTE',
                'priorite' => $data['priorite'] ?? 0,
                'author' => Auth::user()->name ?? 'System',
                'refUser' => Auth::id(),
                'deleted' => 'NON',
            ]);

            return response()->json([
                'message' => 'Ticket créé avec succès',
                'ticket' => $ticket->load(['patient', 'service', 'cabinet']),
            ], 201);
        });
    }

    public function show(Ticket $ticket)
    {
        return $ticket->load(['patient', 'service', 'cabinet', 'appels']);
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->update([
            'deleted' => 'OUI',
            'statut' => 'ANNULE',
        ]);

        return response()->json([
            'message' => 'Ticket annulé avec succès'
        ]);
    }
}