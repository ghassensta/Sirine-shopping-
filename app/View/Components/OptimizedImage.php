<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Services\ImageOptimizer;
use Illuminate\Support\Facades\Storage;

class OptimizedImage extends Component
{
    public $product;
    public $class;
    public $loading;
    public $optimizedImages;
    public $srcset;
    public $sizes;
    public $src;
    
    public function __construct($product, $class = '', $loading = 'lazy')
    {
        $this->product = $product;
        $this->class = $class;
        $this->loading = $loading;
        
        $optimizer = app(ImageOptimizer::class);
        
        // Vérifier si l'image a déjà été optimisée
        if ($product->optimized_images) {
            $this->optimizedImages = json_decode($product->optimized_images, true);
        } elseif ($product->image_avant && Storage::disk('public')->exists($product->image_avant)) {
            // Optimiser à la volée
            $fullPath = storage_path('app/public/' . $product->image_avant);
            $this->optimizedImages = $optimizer->optimize($fullPath, $product->image_avant);
            
            // Sauvegarder pour la prochaine fois
            $product->optimized_images = json_encode($this->optimizedImages);
            $product->save();
        } else {
            $this->optimizedImages = [];
        }
        
        if (!empty($this->optimizedImages)) {
            $this->srcset = $optimizer->generateSrcset($this->optimizedImages);
            $this->sizes = $optimizer->generateSizes();
            $this->src = $optimizer->getBestSource($this->optimizedImages);
        } else {
            $this->srcset = '';
            $this->sizes = '';
            $this->src = asset('storage/' . ($product->image_avant ?? 'default.jpg'));
        }
    }
    
    public function render()
    {
        return view('components.optimized-image');
    }
}
