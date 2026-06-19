<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/salle-attente', function () {
    return Inertia::render('SalleAttente');
});

Route::get('/reception/tickets', function () {
    return Inertia::render('ReceptionTickets');
});

Route::get('/cabinet/medecin', function () {
    return Inertia::render('CabinetMedecin');
});