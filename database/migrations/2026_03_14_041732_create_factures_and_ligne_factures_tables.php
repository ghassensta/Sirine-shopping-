<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero_facture')->unique();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->date('date_facture');
            $table->date('date_echeance')->nullable();
            $table->enum('statut', ['brouillon', 'envoyee', 'payee', 'annulee'])->default('brouillon');
            $table->decimal('remise_globale', 5, 2)->default(0);   // % remise
            $table->decimal('subtotal_ht',   12, 3)->default(0);
            $table->decimal('total_remise',  12, 3)->default(0);
            $table->decimal('total_ttc',     12, 3)->default(0);
            $table->text('notes')->nullable();
            $table->text('conditions')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('ligne_factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facture_id')->constrained('factures')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('designation');
            $table->text('description')->nullable();
            $table->decimal('quantite',      10, 2)->default(1);
            $table->decimal('prix_unitaire', 12, 3)->default(0);
            $table->decimal('remise',         5, 2)->default(0);   // % remise ligne
            $table->decimal('subtotal',      12, 3)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ligne_factures');
        Schema::dropIfExists('factures');
    }
};
