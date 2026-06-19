<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@hopital.com'
            ],
            [
                'name' => 'Super Administrateur',
                'password' => Hash::make('password')
            ]
        );
    }
}