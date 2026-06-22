<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Api\TicketController;

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

Route::get('/tickets/{ticket}/pdf', [TicketController::class, 'printPdf'])
    ->name('tickets.pdf');

Route::get('/patient/tickets', function () {
    return Inertia::render('ReceptionTicketsPatient');
});

Route::get('/admin/services', function () {
    return Inertia::render('Services');
});

Route::get('/admin/cabinets', function () {
    return Inertia::render('Cabinets');
});