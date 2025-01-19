<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $services = Service::all();
            return response()->json($services);
        } catch (\Exception $e) {
            return response()->json("Problème de récupération de la liste des services");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $service = new Service([
                "reference" => $request->input("reference"),
                "designation" => $request->input("designation"),
                "prixUnitaire" => $request->input("prixUnitaire"),
                "TVA" => $request->input("TVA")
            ]);
            $service->save();
            return response()->json($service);
        } catch (\Exception $e) {
            return response()->json("Insertion impossible");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $service = Service::findOrFail($id);
            return response()->json($service);
        } catch (\Exception $e) {
            return response()->json("Problème de récupération des données");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $service = Service::findorFail($id);
            $service->update($request->all());
            return response()->json($service);
        } catch (\Exception $e) {
            return response()->json("Problème de modification");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->delete();
            return response()->json("Service supprimé avec succes");
        } catch (\Exception $e) {
            return response()->json("Problème de suppression du service");
        }
    }

    public function servicesPaginate()
    {
        try {
            $perPage = request()->input('pageSize', 2);
            $services = Service::paginate($perPage);
            return response()->json([
                'services' => $services->items(),
                'totalPages' => $services->lastPage(),
            ]);
        } catch (\Exception $e) {
            return response()->json("Selection impossible {$e->getMessage()}");
        }
    }
}
