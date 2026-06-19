<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueCall extends Model
{
    protected $fillable = [
        'ticket_id',
        'cabinet_id',
        'called_by',
        'numero_appel',
        'called_at',
        'message',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function cabinet()
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'called_by');
    }
}