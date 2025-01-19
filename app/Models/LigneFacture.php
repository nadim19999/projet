<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneFacture extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantite',
        'montantHT',
        'remise',
        'TVA',
        'montantTTC',
        'factureID',
        'serviceID',
    ];

    public function facture()
    {
        return $this->belongsTo(Facture::class, 'factureID');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'serviceID');
    }
}