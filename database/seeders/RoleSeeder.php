<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Reception',
            'Medecin',
            'Infirmier',
            'Caissier',
            'Pharmacien',
            'Ecran Salle Attente',
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['nom' => $role],
                [
                    'description' => null,
                    'statut' => 'ACTIF',
                    'author' => 'Seeder',
                    'refUser' => null,
                    'deleted' => 'NON',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}