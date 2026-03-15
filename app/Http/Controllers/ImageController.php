<?php

namespace App\Http\Controllers;

use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class ImageController extends Controller
{
    protected ImageOptimizer $optimizer;
    
    public function __construct(ImageOptimizer $optimizer)
    {
        $this->optimizer = $optimizer;
    }
    
    /**
     * Optimise une image uploadée
     */
    public function optimize(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240'
        ]);
        
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        
        // Sauvegarder l'image originale
        $path = $file->storeAs('products', $filename, 'public');
        
        // Optimiser l'image
        $fullPath = storage_path('app/public/' . $path);
        $optimized = $this->optimizer->optimize($fullPath, $filename);
        
        return response()->json([
            'success' => true,
            'original' => $path,
            'optimized' => $optimized,
            'srcset' => $this->optimizer->generateSrcset($optimized),
            'sizes' => $this->optimizer->generateSizes(),
            'src' => $this->optimizer->getBestSource($optimized)
        ]);
    }
    
    /**
     * Endpoint pour optimiser les images existantes
     */
    public function optimizeExisting()
    {
        $this->optimizer->optimizeExistingProductImages();
        
        return response()->json([
            'success' => true,
            'message' => 'Optimisation des images existantes terminée'
        ]);
    }
    
    /**
     * Génère une balise img optimisée
     */
    public function generateOptimizedImageTag($product, $class = '', $loading = 'lazy')
    {
        // Vérifier si l'image a déjà été optimisée
        $optimizedImages = [];
        
        if ($product->optimized_images) {
            $optimizedImages = json_decode($product->optimized_images, true);
        } elseif ($product->image_avant && Storage::disk('public')->exists($product->image_avant)) {
            // Optimiser à la volée
            $fullPath = storage_path('app/public/' . $product->image_avant);
            $optimizedImages = $this->optimizer->optimize($fullPath, $product->image_avant);
            
            // Sauvegarder pour la prochaine fois
            $product->optimized_images = json_encode($optimizedImages);
            $product->save();
        }
        
        if (empty($optimizedImages)) {
            // Fallback à l'image originale
            return sprintf(
                '<img src="%s" alt="%s" class="%s" loading="%s" width="400" height="400" decoding="async">',
                asset('storage/' . $product->image_avant),
                htmlspecialchars($product->name),
                $class,
                $loading
            );
        }
        
        $srcset = $this->optimizer->generateSrcset($optimizedImages);
        $sizes = $this->optimizer->generateSizes();
        $src = $this->optimizer->getBestSource($optimizedImages);
        
        return sprintf(
            '<img src="%s" srcset="%s" sizes="%s" alt="%s" class="%s" loading="%s" width="400" height="400" decoding="async" fetchpriority="%s">',
            $src,
            $srcset,
            $sizes,
            htmlspecialchars($product->name),
            $class,
            $loading,
            $loading === 'eager' ? 'high' : 'auto'
        );
    }
}
