<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabinet extends Model
{
    protected $fillable = [
        'service_id',
        'nom_cabinet',
        'numero_cabinet',
        'localisation',
        'statut',
        'author',
        'refUser',
        'deleted'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}