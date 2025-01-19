<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('raisonSociale');
            $table->string('matriculeFiscale');
            $table->string('email');
            $table->integer('numeroTelephone');
            $table->string('adresse');
            $table->integer('codePostal');
            $table->string('ville');
            $table->string('pays');
            $table->boolean('exoneration');
            $table->decimal('encours', 15, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};