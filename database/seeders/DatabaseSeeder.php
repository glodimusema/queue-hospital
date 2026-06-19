<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ServiceSeeder::class,
            CabinetSeeder::class,
            PatientSeeder::class,
            TicketSeeder::class,
            AdminSeeder::class,
        ]);
    }
}