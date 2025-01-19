<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'raisonSociale',
        'matriculeFiscale',
        'email',
        'numeroTelephone',
        'adresse',
        'codePostal',
        'ville',
        'pays',
        'exoneration',
        'encours',
    ];

    public function factures()
    {
        return $this->hasMany(Facture::class, 'clientID');
    }
}