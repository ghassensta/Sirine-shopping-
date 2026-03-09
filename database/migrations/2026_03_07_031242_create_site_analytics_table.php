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
        Schema::create('site_analytics', function (Blueprint $table) {
           $table->id();
            $table->string('visitor_id')->nullable(); // cookie ou session ID
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('page')->nullable(); // page visitée
            $table->string('action')->nullable(); // visite, clic, ajout_panier, commande...
            $table->json('details')->nullable(); // détails supplémentaires (ex: produit, catégorie)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_analytics');
    }
};
