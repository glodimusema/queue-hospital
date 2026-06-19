<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'nom',
        'postnom',
        'prenom',
        'sexe',
        'date_naissance',
        'telephone',
        'adresse',
        'numero_patient',
        'author',
        'refUser',
        'deleted',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}