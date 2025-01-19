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
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numeroFacture');
            $table->date('dateFacture');
            $table->decimal('montantHT', 15, 3);
            $table->decimal('montantTVA', 15, 3);
            $table->decimal('timbre', 15, 2);
            $table->decimal('montantTTC', 15, 3);
            $table->decimal('encours', 15, 3);
            $table->string('etat');
            $table->unsignedBigInteger('clientID');
            $table->foreign('clientID')->references('id')->on('clients')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};