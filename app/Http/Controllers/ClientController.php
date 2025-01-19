<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $clients = Client::all();
            return response()->json($clients);
        } catch (\Exception $e) {
            return response()->json("Problème de récupération de la liste des clients");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $client = new Client([
                "raisonSociale" => $request->input("raisonSociale"),
                "matriculeFiscale" => $request->input("matriculeFiscale"),
                "email" => $request->input("email"),
                "numeroTelephone" => $request->input("numeroTelephone"),
                "adresse" => $request->input("adresse"),
                "codePostal" => $request->input("codePostal"),
                "ville" => $request->input("ville"),
                "pays" => $request->input("pays"),
                "exoneration" => $request->input("exoneration"),
                "encours" => 0
            ]);
            $client->save();
            return response()->json($client);
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
            $client = Client::findOrFail($id);
            return response()->json($client);
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
            $client = Client::findorFail($id);
            $client->update($request->all());
            return response()->json($client);
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
            $client = Client::findOrFail($id);
            $client->delete();
            return response()->json("Client supprimé avec succes");
        } catch (\Exception $e) {
            return response()->json("Problème de suppression du client");
        }
    }

    public function clientsPaginate()
    {
        try {
            $perPage = request()->input('pageSize', 2);
            $clients = Client::paginate($perPage);
            return response()->json([
                'clients' => $clients->items(),
                'totalPages' => $clients->lastPage(),
            ]);
        } catch (\Exception $e) {
            return response()->json("Selection impossible {$e->getMessage()}");
        }
    }
}