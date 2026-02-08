<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // Contenu principal
            $table->string('image_avant')->nullable();
            $table->json('images')->nullable();              // galerie d’images JSON
            $table->string('name');                          // nom du produit
            $table->string('slug')->unique();                // slug SEO
            $table->text('description')->nullable();         // description longue

            // Tarification & stock
            $table->decimal('price', 10, 2);                 // prix de vente
            $table->decimal('price_baree', 10, 2)->default(0);   // prix de vente (base ou unique)
            $table->unsignedInteger('stock')->default(0);    // stock disponible
            $table->string('sku')->unique()->nullable();     // référence interne
            $table->boolean('is_active')->default(true);     // visible ou non


            // Variantes simples (tailles & couleurs) – stockage JSON
            $table->json('available_sizes')->nullable()
                  ->comment('Tableau des tailles disponibles ex: ["S", "M", "L", "XL"]');
            $table->json('available_colors')->nullable()
                  ->comment('Tableau des couleurs ex: [{"name":"Noir","hex":"#000000"}, ...] ou simple ["Noir", "Blanc"]');
            // --- Champs SEO ---
            $table->string('meta_title')->nullable()->comment('Titre SEO (<70 caractères)');
            $table->string('meta_description')->nullable()->comment('Méta-description (<160 caractères)');
            $table->string('meta_keywords')->nullable()->comment('Liste de mots-clés, séparés par des virgules');
            $table->string('og_image')->nullable()->comment('Image Open Graph');

            // Soft deletes & timestamps
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
