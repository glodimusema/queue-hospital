<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $date = now()->toDateString();

        $tickets = [
            ['PAT-0001', 'GEN', 'CAB-GEN-01', 'GEN-001'],
            ['PAT-0002', 'PED', 'CAB-PED-01', 'PED-001'],
            ['PAT-0003', 'GYN', 'CAB-GYN-01', 'GYN-001'],
        ];

        foreach ($tickets as [$numeroPatient, $codeService, $numeroCabinet, $numeroTicket]) {
            $patient = DB::table('patients')->where('numero_patient', $numeroPatient)->first();
            $service = DB::table('services')->where('code_service', $codeService)->first();
            $cabinet = DB::table('cabinets')->where('numero_cabinet', $numeroCabinet)->first();

            if (!$service) {
                continue;
            }

            DB::table('tickets')->updateOrInsert(
                [
                    'numero_ticket' => $numeroTicket,
                    'date_ticket' => $date,
                ],
                [
                    'patient_id' => $patient?->id,
                    'service_id' => $service->id,
                    'cabinet_id' => $cabinet?->id,
                    'statut' => 'EN_ATTENTE',
                    'priorite' => 0,
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