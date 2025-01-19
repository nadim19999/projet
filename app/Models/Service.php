<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'designation',
        'prixUnitaire',
        'TVA'
    ];

    public function ligneFactures()
    {
        return $this->hasMany(LigneFacture::class, 'serviceID');
    }
}