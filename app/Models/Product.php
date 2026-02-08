<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "products";

    protected $fillable = [
        'image_avant',
        'images',
        'name',
        'slug',
        'description',
        'price_baree',          // prix barré / ancien prix
        'price',                // prix actuel / prix de vente
        'stock',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'available_sizes',      // ← ajouté
        'available_colors',     // ← ajouté
    ];

    protected $casts = [
        'images'            => 'array',
        'available_sizes'   => 'array',     // ← JSON → tableau PHP
        'available_colors'  => 'array',     // ← JSON → tableau PHP
        'price_baree'       => 'decimal:3', // si tu veux 3 décimales
        'price'             => 'decimal:3',
        'stock'             => 'integer',
        'is_active'         => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product')
                    ->withTimestamps();
    }

    public function avis()
    {
        return $this->hasMany(Avis::class, 'product_id', 'id');
    }

    // ──────────────────────────────────────────────
    // Accessors pratiques pour l'affichage
    // ──────────────────────────────────────────────

    /**
     * Affiche les tailles sous forme de chaîne lisible
     * Exemple : "S, M, L, XL"
     */
    public function getSizesDisplayAttribute(): string
    {
        if (empty($this->available_sizes)) {
            return '—';
        }

        return implode(', ', (array) $this->available_sizes);
    }

    /**
     * Affiche les couleurs sous forme lisible
     * Gère les deux formats : simple ["Noir", "Blanc"] ou enrichi [{"name":"Noir", "hex":"#000"}]
     */
    public function getColorsDisplayAttribute(): string
    {
        if (empty($this->available_colors)) {
            return '—';
        }

        $colors = (array) $this->available_colors;

        $display = array_map(function ($color) {
            if (is_string($color)) {
                return $color;
            }

            if (is_array($color) && isset($color['name'])) {
                return $color['name'] . (isset($color['hex']) ? ' (' . $color['hex'] . ')' : '');
            }

            return 'Inconnu';
        }, $colors);

        return implode(', ', $display);
    }

    /**
     * Vérifie si le produit propose des variantes (tailles ou couleurs)
     */
    public function getHasVariantsAttribute(): bool
    {
        return !empty($this->available_sizes) || !empty($this->available_colors);
    }

    /**
     * Obtenir la note moyenne des avis approuvés.
     */
    public function getAverageRatingAttribute()
    {
        $approvedReviews = $this->avis()->where('approved', true)->get();

        if ($approvedReviews->isEmpty()) {
            return 5.0; // Note par défaut
        }

        return round($approvedReviews->avg('rating'), 1);
    }

    /**
     * Obtenir le nombre total d'avis approuvés.
     */
    public function getTotalReviewsAttribute()
    {
        return $this->avis()->where('approved', true)->count();
    }

    /**
     * Vérifier si le produit a des avis.
     */
    public function hasReviews()
    {
        return $this->total_reviews > 0;
    }
}