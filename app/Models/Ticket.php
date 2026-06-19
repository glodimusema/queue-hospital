<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'patient_id',
        'service_id',
        'cabinet_id',
        'numero_ticket',
        'date_ticket',
        'statut',
        'priorite',
        'author',
        'refUser',
        'deleted',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function cabinet()
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function appels()
    {
        return $this->hasMany(QueueCall::class);
    }
}