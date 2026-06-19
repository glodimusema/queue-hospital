<?php

namespace App\Events;

use App\Models\QueueCall;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCalled implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(public QueueCall $queueCall)
    {
        $this->queueCall->load([
            'ticket.patient',
            'ticket.service',
            'cabinet',
            'user',
        ]);
    }

    public function broadcastOn(): Channel
    {
        return new Channel('salle-attente');
    }

    public function broadcastAs(): string
    {
        return 'ticket.called';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->queueCall->id,
            'numero_ticket' => $this->queueCall->ticket->numero_ticket,
            'service' => $this->queueCall->ticket->service->nom_service ?? null,
            'cabinet' => $this->queueCall->cabinet->nom_cabinet ?? null,
            'numero_cabinet' => $this->queueCall->cabinet->numero_cabinet ?? null,
            'message' => $this->queueCall->message,
            'called_at' => $this->queueCall->called_at,
        ];
    }
}