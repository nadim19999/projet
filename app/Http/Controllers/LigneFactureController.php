<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\Service;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;

class LigneFactureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $lignefactures = LigneFacture::all();
            return response()->json($lignefactures);
        } catch (\Exception $e) {
            return response()->json("Problème de récupération des ligne factures");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $facture = Facture::findOrFail($request->input("factureID"));
            $client = Client::findOrFail($facture->clientID);
            $service = Service::findOrFail($request->input("serviceID"));
            $TVA = $client->exoneration ? 0 : $service->TVA;
            $quantite = $request->input("quantite");
            $prixUnitaire = $service->prixUnitaire;
            $remise = $request->input("remise");
            $montantHT = $quantite * $prixUnitaire * (1 - $remise / 100);
            $montantTTC = $montantHT * (1 + $TVA / 100);
            $ligne = new LigneFacture([
                "quantite" => $quantite,
                "montantHT" => $montantHT,
                "remise" => $remise,
                "TVA" => $TVA,
                "montantTTC" => $montantTTC,
                "factureID" => $request->input("factureID"),
                "serviceID" => $request->input("serviceID")
            ]);
            $facture->montantHT += $montantHT;
            $facture->montantTVA += $montantHT * ($TVA / 100);
            if ($facture->montantTTC == 0) {
                $facture->montantTTC += $montantTTC + $facture->timbre;
                $client->encours += $montantTTC + $facture->timbre;
                $facture->encours += $montantTTC + $facture->timbre;
            } else {
                $facture->montantTTC += $montantTTC;
                $client->encours += $montantTTC;
                $facture->encours += $montantTTC;
            }
            $ligne->save();
            $facture->save();
            $client->save();
            return response()->json($ligne);
        } catch (\Exception $e) {
            return response()->json("Insertion impossible: " . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $lignefacture = LigneFacture::findOrFail($id);
            return response()->json($lignefacture);
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
            $ligne = LigneFacture::findOrFail($id);
            $facture = Facture::findOrFail($ligne->factureID);
            $client = Client::findOrFail($facture->clientID);
            $service = Service::findOrFail($request->input("serviceID"));
            $TVA = $client->exoneration ? 0 : $service->TVA;
            $facture->montantHT -= $ligne->montantHT;
            $facture->montantTVA -= $ligne->montantHT * ($ligne->TVA / 100);
            $facture->montantTTC -= $ligne->montantTTC;
            $client->encours -= $ligne->montantTTC;
            $facture->encours -= $ligne->montantTTC;
            $quantite = $request->input("quantite");
            $prixUnitaire = $service->prixUnitaire;
            $remise = $request->input("remise");
            $montantHT = $quantite * $prixUnitaire * (1 - $remise / 100);
            $montantTTC = $montantHT * (1 + $TVA / 100);
            $ligne->update([
                "quantite" => $quantite,
                "montantHT" => $montantHT,
                "remise" => $remise,
                "TVA" => $TVA,
                "montantTTC" => $montantTTC,
                "serviceID" => $request->input("serviceID")
            ]);
            $facture->montantHT += $montantHT;
            $facture->montantTVA += $montantHT * ($TVA / 100);
            $facture->montantTTC += $montantTTC;
            $client->encours += $montantTTC;
            $facture->encours += $montantTTC;
            $facture->save();
            $client->save();
            return response()->json("Ligne de facture mise à jour avec succès.");
        } catch (\Exception $e) {
            return response()->json("Problème de mise à jour de la ligne de facture: " . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $ligne = LigneFacture::findOrFail($id);
            $facture = Facture::findOrFail($ligne->factureID);
            $client = Client::findOrFail($facture->clientID);
            $facture->montantHT -= $ligne->montantHT;
            $facture->montantTVA -= $ligne->montantHT * ($ligne->TVA / 100);
            $facture->montantTTC -= $ligne->montantTTC;
            $client->encours -= $ligne->montantTTC;
            $facture->encours -= $ligne->montantTTC;
            $ligne->delete();
            $facture->save();
            $client->save();
            return response()->json("Ligne de facture supprimée avec succès.");
        } catch (\Exception $e) {
            return response()->json("Problème de suppression de la ligne de facture: " . $e->getMessage(), 500);
        }
    }
}