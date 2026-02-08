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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('title_section')->nullable();
            $table->text('sous_title_section')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('set null'); // Pour la hiérarchie
            $table->boolean('is_active')->default(true);
            // SEO Meta fields
            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_publish')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();                            // pour suppression en douceur
            
            // Index pour la hiérarchie
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
