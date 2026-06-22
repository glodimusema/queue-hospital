<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Service;
use App\Models\Cabinet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Patient;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\QueueCall;
use App\Events\TicketCalled;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['patient', 'service', 'cabinet'])
            ->where('deleted', 'NON')
            ->whereDate('date_ticket', now()->toDateString());

        // if ($request->filled('statut')) {
        //     $query->where('statut', $request->statut);
        // }

        if ($request->filled('statut')) {
            $statuts = explode(',', $request->statut);
            $query->whereIn('statut', $statuts);
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('cabinet_id')) {
            $query->where('cabinet_id', $request->cabinet_id);
        }

        return $query
            ->orderByDesc('priorite')
            ->orderBy('created_at')
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


    public function call(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'cabinet_id' => 'required|exists:cabinets,id',
        ]);

        return DB::transaction(function () use ($ticket, $data) {

            $numeroAppel = QueueCall::where('ticket_id', $ticket->id)->count() + 1;

            $ticket->update([
                'cabinet_id' => $data['cabinet_id'],
                'statut' => 'APPELE',
            ]);

            $queueCall = QueueCall::create([
                'ticket_id' => $ticket->id,
                'cabinet_id' => $data['cabinet_id'],
                'called_by' => Auth::id(),
                'numero_appel' => $numeroAppel,
                'called_at' => now(),
                'message' => "Ticket {$ticket->numero_ticket}, veuillez vous présenter au cabinet.",
            ]);

            event(new TicketCalled($queueCall));

            return response()->json([
                'message' => 'Ticket appelé avec succès',
                'call' => $queueCall->load(['ticket.patient', 'ticket.service', 'cabinet', 'user']),
            ]);
        });
    }



    public function markStatus(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'statut' => 'required|in:EN_ATTENTE,APPELE,EN_CONSULTATION,TERMINE,ABSENT,ANNULE',
        ]);

        $ticket->update([
            'statut' => $data['statut'],
        ]);

        return response()->json([
            'message' => 'Statut modifié avec succès',
            'ticket' => $ticket->load(['patient', 'service', 'cabinet']),
        ]);
    }


    public function recall(Ticket $ticket)
    {
        return DB::transaction(function () use ($ticket) {

            $numeroAppel = QueueCall::where('ticket_id', $ticket->id)->count() + 1;

            $queueCall = QueueCall::create([
                'ticket_id' => $ticket->id,
                'cabinet_id' => $ticket->cabinet_id,
                'called_by' => Auth::id(),
                'numero_appel' => $numeroAppel,
                'called_at' => now(),
                'message' => "Rappel. Ticket {$ticket->numero_ticket}, veuillez vous présenter au cabinet.",
            ]);

            event(new TicketCalled($queueCall));

            return response()->json([
                'message' => 'Ticket rappelé avec succès',
                'call' => $queueCall->load(['ticket.patient', 'ticket.service', 'cabinet', 'user']),
            ]);
        });
    }


    public function kioskStore(Request $request)
    {
        $data = $request->validate([
            'cabinet_id' => 'required|exists:cabinets,id',
            'priorite' => 'nullable|integer|min:0',
        ]);

        return DB::transaction(function () use ($data) {

            $cabinet = Cabinet::with('service')
                ->where('id', $data['cabinet_id'])
                ->where('deleted', 'NON')
                ->where('statut', 'ACTIF')
                ->firstOrFail();

            $service = $cabinet->service;

            if (!$service) {
                abort(422, 'Ce cabinet n’est lié à aucun service.');
            }

            $date = now()->toDateString();
            $codeService = $service->code_service ?: 'TCK';

            $dernierNumero = Ticket::where('cabinet_id', $cabinet->id)
                ->whereDate('date_ticket', $date)
                ->lockForUpdate()
                ->count() + 1;

            $numeroTicket = $codeService . '-' . $cabinet->numero_cabinet . '-' . str_pad($dernierNumero, 3, '0', STR_PAD_LEFT);

            $patient = Patient::create([
                'nom' => $numeroTicket,
                'postnom' => null,
                'prenom' => null,
                'numero_patient' => 'AUTO-' . now()->format('YmdHis') . '-' . rand(100, 999),
                'author' => 'Kiosque',
                'deleted' => 'NON',
            ]);

            $ticket = Ticket::create([
                'patient_id' => $patient->id,
                'service_id' => $service->id,
                'cabinet_id' => $cabinet->id,
                'numero_ticket' => $numeroTicket,
                'date_ticket' => $date,
                'statut' => 'EN_ATTENTE',
                'priorite' => $data['priorite'] ?? 0,
                'author' => 'Kiosque',
                'deleted' => 'NON',
            ]);

            return response()->json([
                'message' => 'Ticket généré avec succès',
                'ticket' => $ticket->load(['patient', 'service', 'cabinet']),
                'print_url' => url("/tickets/{$ticket->id}/pdf"),
            ], 201);
        });
    }

    public function printPdf(Ticket $ticket)
    {
        $ticket->load(['patient', 'service', 'cabinet']);

        $pdf = Pdf::loadView('pdf.ticket-a6', [
            'ticket' => $ticket,
        ])->setPaper('a6', 'portrait');

        return $pdf->stream('ticket-' . $ticket->numero_ticket . '.pdf');
    }
}