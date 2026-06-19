<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            ['PAT-0001', 'MALU', 'KASEREKA', 'Jean', 'M', '1995-04-12', '0990000001', 'Goma'],
            ['PAT-0002', 'KAVIRA', null, 'Marie', 'F', '2001-08-20', '0990000002', 'Goma'],
            ['PAT-0003', 'MUGISHA', 'BAHATI', 'Paul', 'M', '1988-01-10', '0990000003', 'Karisimbi'],
        ];

        foreach ($patients as [$numero, $nom, $postnom, $prenom, $sexe, $dateNaissance, $telephone, $adresse]) {
            DB::table('patients')->updateOrInsert(
                ['numero_patient' => $numero],
                [
                    'nom' => $nom,
                    'postnom' => $postnom,
                    'prenom' => $prenom,
                    'sexe' => $sexe,
                    'date_naissance' => $dateNaissance,
                    'telephone' => $telephone,
                    'adresse' => $adresse,
                    'author' => 'Seeder',
                    'refUser' => null,
                    'deleted' => 'NON',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}