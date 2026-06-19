<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['GEN', 'Médecine Générale'],
            ['PED', 'Pédiatrie'],
            ['GYN', 'Gynécologie'],
            ['URG', 'Urgence'],
            ['LAB', 'Laboratoire'],
        ];

        foreach ($services as [$code, $nom]) {
            DB::table('services')->updateOrInsert(
                ['code_service' => $code],
                [
                    'nom_service' => $nom,
                    'description' => null,
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