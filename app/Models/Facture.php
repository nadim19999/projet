<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'numeroFacture',
        'dateFacture',
        'montantHT',
        'montantTVA',
        'timbre',
        'montantTTC',
        'encours',
        'etat',
        'clientID',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($facture) {
            $facture->numeroFacture = self::generateNumeroFacture();
        });
    }

    public static function generateNumeroFacture()
    {
        $year = date('y');
        $lastInvoice = self::whereYear('dateFacture', date('Y'))
            ->orderBy('numeroFacture', 'desc')
            ->first();
        if ($lastInvoice) {
            $lastNumber = substr($lastInvoice->numeroFacture, -4);
            $newNumber = str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        return 'Fac-' . $year . $newNumber;
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientID');
    }

    public function ligneFactures()
    {
        return $this->hasMany(LigneFacture::class, 'factureID');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'factureID');
    }
}
