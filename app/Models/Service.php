<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'nom_service',
        'code_service',
        'description',
        'statut',
        'author',
        'refUser',
        'deleted',
    ];

    public function cabinets()
    {
        return $this->hasMany(Cabinet::class);
    }
}