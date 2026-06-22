<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cabinet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CabinetController extends Controller
{
    public function index(Request $request)
    {
        $query = Cabinet::with('service')
            ->where('deleted', 'NON')
            ->latest();

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        return $query->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'nom_cabinet' => 'required|string|max:191',
            'numero_cabinet' => 'nullable|string|max:50',
            'localisation' => 'nullable|string|max:191',
            'statut' => 'nullable|in:ACTIF,INACTIF',
        ]);

        $data['author'] = Auth::user()->name ?? 'System';
        $data['refUser'] = Auth::id();
        $data['deleted'] = 'NON';

        $cabinet = Cabinet::create($data);

        return response()->json([
            'message' => 'Cabinet créé avec succès',
            'cabinet' => $cabinet->load('service'),
        ], 201);
    }

    public function show(Cabinet $cabinet)
    {
        return $cabinet->load('service');
    }

    public function update(Request $request, Cabinet $cabinet)
    {
        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'nom_cabinet' => 'required|string|max:191',
            'numero_cabinet' => 'nullable|string|max:50',
            'localisation' => 'nullable|string|max:191',
            'statut' => 'nullable|in:ACTIF,INACTIF',
        ]);

        $cabinet->update($data);

        return response()->json([
            'message' => 'Cabinet modifié avec succès',
            'cabinet' => $cabinet->load('service'),
        ]);
    }

    public function destroy(Cabinet $cabinet)
    {
        $cabinet->update([
            'deleted' => 'OUI',
            'statut' => 'INACTIF',
        ]);

        return response()->json([
            'message' => 'Cabinet supprimé avec succès',
        ]);
    }

    public function actifs()
    {
        return response()->json([
            'data' => Cabinet::with('service')
                ->where('statut', 'ACTIF')
                ->orderBy('nom_cabinet')
                ->get()
        ]);
    }

    public function updateStatut(Request $request, Cabinet $cabinet)
    {
        $data = $request->validate([
            'statut' => 'required|in:ACTIF,INACTIF',
        ]);

        $cabinet->update([
            'statut' => $data['statut'],
        ]);

        return response()->json([
            'message' => $data['statut'] === 'ACTIF'
                ? 'Cabinet activé avec succès'
                : 'Cabinet désactivé avec succès',
            'data' => $cabinet,
        ]);
    }



}