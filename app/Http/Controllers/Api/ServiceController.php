<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        return Service::where('deleted', 'NON')
            ->latest()
            ->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom_service' => 'required|string|max:191',
            'code_service' => 'nullable|string|max:20|unique:services,code_service',
            'description' => 'nullable|string|max:255',
            'statut' => 'nullable|in:ACTIF,INACTIF',
        ]);

        $data['author'] = Auth::user()->name ?? 'System';
        $data['refUser'] = Auth::id();
        $data['deleted'] = 'NON';

        $service = Service::create($data);

        return response()->json([
            'message' => 'Service créé avec succès',
            'service' => $service,
        ], 201);
    }

    public function show(Service $service)
    {
        return $service->load('cabinets');
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'nom_service' => 'required|string|max:191',
            'code_service' => 'nullable|string|max:20|unique:services,code_service,' . $service->id,
            'description' => 'nullable|string|max:255',
            'statut' => 'nullable|in:ACTIF,INACTIF',
        ]);

        $service->update($data);

        return response()->json([
            'message' => 'Service modifié avec succès',
            'service' => $service,
        ]);
    }

    public function destroy(Service $service)
    {
        $service->update([
            'deleted' => 'OUI',
            'statut' => 'INACTIF',
        ]);

        return response()->json([
            'message' => 'Service supprimé avec succès',
        ]);
    }
}