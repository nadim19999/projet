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
        Schema::create('ligne_factures', function (Blueprint $table) {
            $table->id();
            $table->decimal('quantite', 15, 3);
            $table->decimal('montantHT', 15, 3);
            $table->decimal('remise', 4, 2);
            $table->decimal('TVA', 4, 2);
            $table->decimal('montantTTC', 15, 3);
            $table->unsignedBigInteger('factureID');
            $table->foreign('factureID')->references('id')->on('factures')->onDelete('restrict');
            $table->unsignedBigInteger('serviceID');
            $table->foreign('serviceID')->references('id')->on('services')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_factures');
    }
};