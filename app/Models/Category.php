<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'image',
        'name',
        'slug',
        'parent_id',
        'is_active',
        'title_section',
        'sous_title_section',
        'is_publish',
        'order',
        // SEO
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
        'is_publish'=>'boolean'
    ];

    /* =======================
     | Relations
     ======================= */
    
    // Relation avec les produits de cette catégorie (many-to-many)
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product')
                    ->withTimestamps();
    }
    
    // Relation parent (catégorie parente)
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
    
    // Relation children (sous-catégories)
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    
    // Relation récursive pour obtenir tous les descendants
    public function descendants()
    {
        return $this->children()->with('descendants');
    }
    
    // Relation récursive pour obtenir tous les ancêtres
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    /* =======================
     | Model Events
     ======================= */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }

            // SEO fallback
            $category->meta_title ??= $category->name;
        });

        static::updating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }

            // SEO fallback
            $category->meta_title ??= $category->name;
        });
    }

    /* =======================
     | Accessors
     ======================= */

    // Image URL
    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/no-image.png');
    }

    // Title section avec fallback sur name
    public function getSectionTitleAttribute()
    {
        return $this->title_section ?: $this->name;
    }

    // Sous title section avec fallback sur meta_description
    public function getSectionSubtitleAttribute()
    {
        return $this->sous_title_section ?: $this->meta_description;
    }

    // SEO title fallback
    public function getSeoTitleAttribute()
    {
        return $this->meta_title ?: $this->name;
    }

    // SEO description fallback
    public function getSeoDescriptionAttribute()
    {
        return $this->meta_description
            ?: Str::limit(strip_tags($this->name), 160);
    }

    /**
     * Obtenir la note moyenne des produits de cette catégorie ET de ses sous-catégories
     */
    public function getAverageRatingAttribute()
    {
        // Récupérer tous les IDs de cette catégorie et de ses descendants
        $categoryIds = $this->getAllCategoryIds();
        
        // Calculer la moyenne des avis approuvés pour tous les produits concernés
        $avgRating = Product::whereHas('categories', function($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        })
            ->whereHas('avis', function($query) {
                $query->where('approved', true);
            })
            ->withAvg(['avis' => function($query) {
                $query->where('approved', true);
            }], 'rating')
            ->get()
            ->avg('avis_avg_rating');
            
        return $avgRating ? round($avgRating, 1) : 5.0; // Note par défaut
    }

    /**
     * Obtenir le nombre total d'avis approuvés pour cette catégorie ET ses sous-catégories
     */
    public function getTotalReviewsAttribute()
    {
        // Récupérer tous les IDs de cette catégorie et de ses descendants
        $categoryIds = $this->getAllCategoryIds();
        
        return Product::whereIn('category_id', $categoryIds)
            ->withCount(['avis' => function($query) {
                $query->where('approved', true);
            }])
            ->get()
            ->sum('avis_count');
    }

    /**
     * Vérifier si la catégorie a des avis.
     */
    public function hasReviews()
    {
        return $this->total_reviews > 0;
    }
    
    /**
     * Obtenir le chemin complet de la catégorie (Parent > Sous-catégorie > Sous-sous)
     */
    public function getFullNameAttribute()
    {
        $names = collect();
        
        // Ajouter les ancêtres d'abord
        if ($this->parent) {
            $ancestors = $this->parent->ancestors;
            if ($ancestors) {
                $names = $ancestors->pluck('name')->reverse();
            }
            $names->push($this->parent->name);
        }
        
        // Ajouter cette catégorie
        $names->push($this->name);
        
        return $names->join(' > ');
    }
    
    /**
     * Obtenir tous les IDs de cette catégorie et de ses descendants (récursif)
     */
    private function getAllCategoryIds()
    {
        $ids = collect([$this->id]);
        
        foreach ($this->children as $child) {
            $ids = $ids->merge($child->getAllCategoryIds());
        }
        
        return $ids->unique();
    }
    
    /**
     * Obtenir le nombre total de produits (incluant les sous-catégories)
     */
    public function getTotalProductsCountAttribute()
    {
        $categoryIds = $this->getAllCategoryIds();
        return Product::whereHas('categories', function($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        })->count();
    }
    
    /**
     * Vérifier si la catégorie est une catégorie parente (n'a pas de parent)
     */
    public function isParent()
    {
        return is_null($this->parent_id);
    }
    
    /**
     * Vérifier si la catégorie est une sous-catégorie (a un parent)
     */
    public function isChild()
    {
        return !is_null($this->parent_id);
    }
    
    /**
     * Obtenir le niveau de profondeur (0 = racine)
     */
    public function getDepthAttribute()
    {
        if (!$this->parent) {
            return 0;
        }
        
        return $this->parent->depth + 1;
    }
    
    /* =======================
     | Scopes
     ======================= */
    
    /**
     * Scope pour n'obtenir que les catégories parentes (racines)
     */
    public function scopeOnlyParents($query)
    {
        return $query->whereNull('parent_id');
    }
    
    /**
     * Scope pour n'obtenir que les catégories actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope pour obtenir les catégories avec leurs produits et sous-catégories
     */
    public function scopeWithProductsAndChildren($query)
    {
        return $query->with(['products', 'children.products']);
    }
    
    /**
     * Scope pour obtenir les catégories ordonnées hiérarchiquement
     */
    public function scopeOrderedHierarchy($query)
    {
        return $query->orderBy('parent_id')->orderBy('name');
    }
    
    /**
     * Scope pour les catégories populaires (avec le plus de produits)
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount('products')
                    ->orderBy('products_count', 'desc')
                    ->limit($limit);
    }
    
    /**
     * Scope pour obtenir une arborescence complète
     */
    public function scopeTree($query)
    {
        return $query->onlyParents()
                    ->with(['children' => function($query) {
                        $query->with(['children' => function($query) {
                            $query->with('children');
                        }]);
                    }]);
    }
    
    /**
     * Obtenir la liste hiérarchique des catégories pour les selects
     */
    public static function getHierarchicalList($excludeId = null, $parentId = null, $level = 0)
    {
        $query = self::where('is_active', true)
                     ->where('parent_id', $parentId)
                     ->orderBy('name');

        // Si excludeId est fourni, exclure cette catégorie et tous ses descendants
        if ($excludeId) {
            $excludedIds = collect([$excludeId]);
            $excludeCategory = self::find($excludeId);
            if ($excludeCategory) {
                $excludedIds = $excludedIds->merge($excludeCategory->getDescendantIds());
            }
            $query->whereNotIn('id', $excludedIds);
        }

        $categories = $query->get();
        
        $list = collect();
        
        foreach ($categories as $category) {
            $indent = str_repeat('—', $level) . ($level > 0 ? ' ' : '');
            $list->push([
                'id' => $category->id,
                'name' => $indent . $category->name,
                'level' => $level
            ]);
            
            // Récursif pour les enfants
            $children = self::getHierarchicalList($excludeId, $category->id, $level + 1);
            $list = $list->merge($children);
        }
        
        return $list;
    }
    
    /**
     * Vérifier si une catégorie est un descendant d'une autre
     */
    public function isDescendantOf($categoryId)
    {
        if ($this->parent_id == $categoryId) {
            return true;
        }
        
        if ($this->parent) {
            return $this->parent->isDescendantOf($categoryId);
        }
        
        return false;
    }
    
    /**
     * Obtenir tous les descendants (IDs)
     */
    public function getDescendantIds()
    {
        $ids = collect();
        
        foreach ($this->children as $child) {
            $ids->push($child->id);
            $ids = $ids->merge($child->getDescendantIds());
        }
        
        return $ids;
    }
    
    /**
     * Obtenir le nom du parent avec fallback
     */
    public function getParentNameAttribute()
    {
        return $this->parent ? $this->parent->name : '—';
    }
    
    /**
     * Obtenir le chemin hiérarchique formaté
     */
    public function getHierarchicalNameAttribute()
    {
        return $this->full_name;
    }
}
