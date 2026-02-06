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
        // Cette migration est vide car la table blogs est déjà créée
        // dans la migration 2025_07_13_172908_create_inspirations_table.php
        // Ne fait rien pour éviter les conflits
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne fait rien
    }
};
