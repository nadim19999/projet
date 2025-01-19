<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Facture;
use App\Models\Paiement;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $paiements = Paiement::all();
            return response()->json($paiements);
        } catch (\Exception $e) {
            return response()->json("Problème de récupération des paiements");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'factureID' => 'required|exists:factures,id',
            'montant' => 'required|numeric|min:0.01',
        ]);
        try {
            $facture = Facture::findOrFail($request->input("factureID"));
            $client = Client::findOrFail($facture->clientID);
            $montant = $request->input("montant");
            if ($montant > $facture->encours) {
                return response()->json("Le montant dépasse l'encours de la facture.", 400);
            }
            $paiement = new Paiement([
                "montant" => $montant,
                "factureID" => $request->input("factureID")
            ]);
            $client->encours -= $montant;
            $facture->encours -= $montant;
            if ($facture->encours == 0) {
                $facture->etat = "Payé";
            } else {
                $facture->etat = "Partiellement payé";
            }
            $paiement->save();
            $facture->save();
            $client->save();
            return response()->json($paiement);
        } catch (\Exception $e) {
            return response()->json("Insertion impossible: " . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $paiement = Paiement::findOrFail($id);
            return response()->json($paiement);
        } catch (\Exception $e) {
            return response()->json("Problème de récupération des données");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0.01',
        ]);
        try {
            $paiement = Paiement::findOrFail($id);
            $facture = Facture::findOrFail($paiement->factureID);
            $client = Client::findOrFail($facture->clientID);
            $ancienMontant = $paiement->montant;
            $nouveauMontant = $request->input('montant');
            if ($nouveauMontant - $ancienMontant > $facture->encours) {
                return response()->json("Le nouveau montant dépasse l'encours de la facture.", 400);
            }
            $facture->encours += $ancienMontant - $nouveauMontant;
            $client->encours += $ancienMontant - $nouveauMontant;
            if ($facture->encours == 0) {
                $facture->etat = "Payé";
            } else {
                $facture->etat = "Partiellement payé";
            }
            $paiement->montant = $nouveauMontant;
            $paiement->save();
            $facture->save();
            $client->save();
            return response()->json($paiement);
        } catch (\Exception $e) {
            return response()->json("Mise à jour impossible: " . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $paiement = Paiement::findOrFail($id);
            $facture = Facture::findOrFail($paiement->factureID);
            $client = Client::findOrFail($facture->clientID);
            $montant = $paiement->montant;
            $facture->encours += $montant;
            $client->encours += $montant;
            if ($facture->encours == 0) {
                $facture->etat = "Payé";
            } elseif ($facture->encours == $facture->montantTTC) {
                $facture->etat = "Non payé";
            } else {
                $facture->etat = "Partiellement payé";
            }
            $paiement->delete();
            $facture->save();
            $client->save();
            return response()->json("Paiement supprimé avec succès.");
        } catch (\Exception $e) {
            return response()->json("Suppression impossible: " . $e->getMessage(), 500);
        }
    }

    public function paiementsPaginate()
    {
        try {
            $perPage = request()->input('pageSize', 2);
            $paiements = Paiement::paginate($perPage);
            return response()->json([
                'paiements' => $paiements->items(),
                'totalPages' => $paiements->lastPage(),
            ]);
        } catch (\Exception $e) {
            return response()->json("Selection impossible {$e->getMessage()}");
        }
    }
}