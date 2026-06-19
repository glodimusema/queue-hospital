<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CabinetSeeder extends Seeder
{
    public function run(): void
    {
        $cabinets = [
            ['GEN', 'Cabinet Médecine Générale', 'CAB-GEN-01', 'Bloc A'],
            ['PED', 'Cabinet Pédiatrie', 'CAB-PED-01', 'Bloc A'],
            ['GYN', 'Cabinet Gynécologie', 'CAB-GYN-01', 'Bloc B'],
            ['URG', 'Cabinet Urgence', 'CAB-URG-01', 'Bloc Urgence'],
            ['LAB', 'Laboratoire', 'LAB-01', 'Bloc Technique'],
        ];

        foreach ($cabinets as [$codeService, $nom, $numero, $localisation]) {
            $service = DB::table('services')->where('code_service', $codeService)->first();

            if (!$service) {
                continue;
            }

            DB::table('cabinets')->updateOrInsert(
                ['numero_cabinet' => $numero],
                [
                    'service_id' => $service->id,
                    'nom_cabinet' => $nom,
                    'localisation' => $localisation,
                    'statut' => 'ACTIF',
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