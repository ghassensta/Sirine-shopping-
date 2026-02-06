<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    protected $table = 'blogs';

    protected $fillable = [
        'image',
        'title',
        'slug',
        'resume',
        'description',
        'meta_title',
        'meta_description',
        'gallery',
        'is_active',
    ];

    protected $casts = [
        'slug' => 'string',
        'is_active' => 'boolean',
        'gallery' => 'array',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Scope for active inspirations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically generate unique slug if not provided
        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
                $baseSlug = $blog->slug;
                $counter = 1;
                while (static::where('slug', $blog->slug)->exists()) {
                    $blog->slug = $baseSlug . '-' . $counter++;
                }
            }
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('title') && empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
                $baseSlug = $blog->slug;
                $counter = 1;
                while (static::where('slug', $blog->slug)->where('id', '!=', $blog->id)->exists()) {
                    $blog->slug = $baseSlug . '-' . $counter++;
                }
            }
        });
    }
}
