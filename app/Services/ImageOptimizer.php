<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    protected ImageManager $manager;
    
    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }
    
    /**
     * Optimise une image et génère plusieurs tailles en WebP
     */
    public function optimize($imagePath, $originalName = null): array
    {
        $originalName = $originalName ?? basename($imagePath);
        $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        
        // Créer le répertoire optimisé s'il n'existe pas
        $optimizedPath = storage_path('app/public/optimized');
        if (!is_dir($optimizedPath)) {
            mkdir($optimizedPath, 0755, true);
        }
        
        $sizes = [320, 640, 768, 1024, 1280, 1536];
        $optimizedImages = [];
        
        try {
            // Charger l'image originale
            $image = $this->manager->read($imagePath);
            
            foreach ($sizes as $width) {
                // Redimensionner en gardant le ratio
                $resized = $image->scale($width, null);
                
                // Encoder en WebP avec qualité 85%
                $webpPath = "{$optimizedPath}/{$width}_{$filename}.webp";
                $resized->toWebp(85)->save($webpPath);
                
                // Aussi générer en format original pour fallback
                $fallbackPath = "{$optimizedPath}/{$width}_{$filename}.{$extension}";
                $resized->save($fallbackPath);
                
                $optimizedImages[] = [
                    'width' => $width,
                    'webp' => "storage/optimized/{$width}_{$filename}.webp",
                    'fallback' => "storage/optimized/{$width}_{$filename}.{$extension}"
                ];
            }
            
            // Générer l'image miniature (thumbnail)
            $thumbnail = $image->scale(150, null);
            $thumbnailPath = "{$optimizedPath}/thumb_{$filename}.webp";
            $thumbnail->toWebp(85)->save($thumbnailPath);
            
            $optimizedImages[] = [
                'width' => 150,
                'webp' => "storage/optimized/thumb_{$filename}.webp",
                'fallback' => "storage/optimized/thumb_{$filename}.{$extension}",
                'is_thumbnail' => true
            ];
            
        } catch (\Exception $e) {
            \Log::error("Erreur optimisation image: " . $e->getMessage());
            return [];
        }
        
        return $optimizedImages;
    }
    
    /**
     * Génère le srcset pour une image optimisée
     */
    public function generateSrcset(array $optimizedImages): string
    {
        $srcset = [];
        
        foreach ($optimizedImages as $image) {
            if (!isset($image['is_thumbnail'])) {
                // Priorité au WebP avec fallback
                $srcset[] = asset($image['webp']) . " {$image['width']}w";
            }
        }
        
        return implode(', ', $srcset);
    }
    
    /**
     * Génère le sizes attribute pour responsive images
     */
    public function generateSizes(): string
    {
        return "(max-width: 640px) 100vw, (max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw";
    }
    
    /**
     * Retourne la meilleure source pour l'image principale
     */
    public function getBestSource(array $optimizedImages): string
    {
        // Chercher l'image de taille moyenne (768px) pour le src par défaut
        foreach ($optimizedImages as $image) {
            if ($image['width'] === 768) {
                return asset($image['webp']);
            }
        }
        
        // Fallback à la première image disponible
        return isset($optimizedImages[0]) ? asset($optimizedImages[0]['webp']) : '';
    }
    
    /**
     * Optimise toutes les images des produits existants
     */
    public function optimizeExistingProductImages(): void
    {
        $products = \App\Models\Product::whereNotNull('image_avant')->get();
        
        foreach ($products as $product) {
            if ($product->image_avant && Storage::disk('public')->exists($product->image_avant)) {
                $fullPath = storage_path('app/public/' . $product->image_avant);
                $optimized = $this->optimize($fullPath, $product->image_avant);
                
                if (!empty($optimized)) {
                    // Sauvegarder les informations d'optimisation
                    $product->optimized_images = json_encode($optimized);
                    $product->save();
                    
                    echo "Optimisé: {$product->name}\n";
                }
            }
        }
    }
}
