<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Client;
use App\Models\LigneFacture;
use App\Models\Service;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $factures = Facture::all();
            return response()->json($factures);
        } catch (\Exception $e) {
            return response()->json("Problème de récupération des factures");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $client = Client::findOrFail($request->input("clientID"));
            $timbre = $client->exoneration ? 0 : 1;
            $facture = new Facture([
                "dateFacture" => $request->input("dateFacture"),
                "montantHT" => 0,
                "montantTVA" => 0,
                "timbre" => $timbre,
                "montantTTC" => 0,
                "encours" => 0,
                "etat" => "Non payé",
                "clientID" => $request->input("clientID")
            ]);
            $facture->save();
            return response()->json($facture);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $facture = Facture::findOrFail($id);
            return response()->json($facture);
        } catch (\Exception $e) {
            return response()->json("Problème de récupération des données");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    /*
    public function update(Request $request, $id)
    {
        try {
            $facture = Facture::findorFail($id);
            $facture->update($request->all());
            return response()->json($facture);
        } catch (\Exception $e) {
            return response()->json("Problème de modification");
        }
    }
    */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $facture = Facture::findOrFail($id);
            $derniereFacture = Facture::latest('id')->first();
            if (!$derniereFacture || $derniereFacture->id !== $facture->id) {
                return response()->json("Impossible de supprimer cette facture.", 400);
            }
            $facture->delete();
            return response()->json("Dernière facture supprimée avec succès.");
        } catch (\Exception $e) {
            return response()->json("Problème de suppression de la facture : " . $e->getMessage(), 500);
        }
    }

    public function facturesPaginate()
    {
        try {
            $perPage = request()->input('pageSize', 2);
            $factures = Facture::paginate($perPage);
            return response()->json([
                'factures' => $factures->items(),
                'totalPages' => $factures->lastPage(),
            ]);
        } catch (\Exception $e) {
            return response()->json("Selection impossible {$e->getMessage()}");
        }
    }

    public function getFacturesForClient($clientId)
    {
        try {
            $factures = Facture::where('clientID', $clientId)->get();
            if ($factures->isEmpty()) {
                return response()->json("Aucune facture trouvée pour ce client.", 404);
            }
            return response()->json($factures);
        } catch (\Exception $e) {
            return response()->json("Problème de récupération des factures pour ce client : " . $e->getMessage(), 500);
        }
    }

    public function getFactureDetails($factureId)
    {
        try {
            $facture = Facture::findOrFail($factureId);
            $client = Client::findOrFail($facture->clientID);
            $lignesFactures = LigneFacture::where('factureID', $factureId)->get();
            $lignes = $lignesFactures->map(function ($ligne) {
                $service = Service::findOrFail($ligne->serviceID);
                return [
                    'id' => $ligne->id,
                    'quantite' => $ligne->quantite,
                    'montantHT' => $ligne->montantHT,
                    'remise' => $ligne->remise,
                    'TVA' => $ligne->TVA,
                    'montantTTC' => $ligne->montantTTC,
                    'service' => [
                        'reference' => $service->reference,
                        'designation' => $service->designation,
                    ],
                ];
            });
            $response = [
                'numeroFacture' => $facture->numeroFacture,
                'dateFacture' => $facture->dateFacture,
                'montantHT' => $facture->montantHT,
                'montantTVA' => $facture->montantTVA,
                'timbre' => $facture->timbre,
                'montantTTC' => $facture->montantTTC,
                'etat' => $facture->etat,
                'encours' => $facture->encours,
                'client' => [
                    'raisonSociale' => $client->raisonSociale,
                    'matriculeFiscale' => $client->matriculeFiscale,
                    'adresse' => $client->adresse,
                    'codePostal' => $client->codePostal,
                    'ville' => $client->ville,
                    'pays' => $client->pays,
                ],
                'lignesFactures' => $lignes,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération de la facture'], 500);
        }
    }
}
