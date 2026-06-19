<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::where('deleted', 'NON')->latest();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('postnom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('telephone', 'like', "%{$search}%")
                    ->orWhere('numero_patient', 'like', "%{$search}%");
            });
        }

        return $query->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:191',
            'postnom' => 'nullable|string|max:191',
            'prenom' => 'nullable|string|max:191',
            'sexe' => 'nullable|string|max:10',
            'date_naissance' => 'nullable|date',
            'telephone' => 'nullable|string|max:50',
            'adresse' => 'nullable|string|max:191',
        ]);

        $data['numero_patient'] = $this->generateNumeroPatient();
        $data['author'] = Auth::user()->name ?? 'System';
        $data['refUser'] = Auth::id();
        $data['deleted'] = 'NON';

        $patient = Patient::create($data);

        return response()->json([
            'message' => 'Patient créé avec succès',
            'patient' => $patient,
        ], 201);
    }

    public function show(Patient $patient)
    {
        return $patient->load('tickets.service', 'tickets.cabinet');
    }

    public function update(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:191',
            'postnom' => 'nullable|string|max:191',
            'prenom' => 'nullable|string|max:191',
            'sexe' => 'nullable|string|max:10',
            'date_naissance' => 'nullable|date',
            'telephone' => 'nullable|string|max:50',
            'adresse' => 'nullable|string|max:191',
        ]);

        $patient->update($data);

        return response()->json([
            'message' => 'Patient modifié avec succès',
            'patient' => $patient,
        ]);
    }

    public function destroy(Patient $patient)
    {
        $patient->update([
            'deleted' => 'OUI',
        ]);

        return response()->json([
            'message' => 'Patient supprimé avec succès',
        ]);
    }

    private function generateNumeroPatient(): string
    {
        $count = Patient::count() + 1;

        return 'PAT-' . now()->format('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}