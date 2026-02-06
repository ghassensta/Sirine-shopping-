<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * Display categories page
     */
    public function index()
    {
        return view('admin.category.index');
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'parent_id' => $category->parent_id,
            'title_section' => $category->title_section,
            'sous_title_section' => $category->sous_title_section,
            'is_active' => $category->is_active,
            'meta_title' => $category->meta_title,
            'meta_description' => $category->meta_description,
            'meta_keywords' => $category->meta_keywords,
            'image_url' => $category->image ? asset('storage/' . $category->image) : null,
            'created_at' => $category->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $category->updated_at->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Data for datatable (server side)
     */
    public function get(Request $request)
    {
        $query = Category::with('parent');

        return DataTables::of($query)
            ->addColumn('image', function ($row) {
                $url = $row->image ? asset('storage/' . $row->image) : asset('images/no-image.png');
                return '<img src="' . e($url) . '" alt="' . e($row->name) . '" width="50">';
            })
            ->addColumn('is_active', function ($row) {
                $class = $row->is_active ? 'bg-label-success' : 'bg-label-warning';
                $text  = $row->is_active ? 'Active' : 'Inactive';
                return '<span class="badge ' . $class . '">' . $text . '</span>';
            })
            ->addColumn('parent_name', function ($row) {
                return $row->parent_name;
            })
            ->addColumn('hierarchical_name', function ($row) {
                return $row->hierarchical_name;
            })
            ->addColumn('image_url', function ($row) {
                return $row->image ? asset('storage/' . $row->image) : null;
            })
            ->rawColumns(['image', 'is_active'])
            ->make(true);
    }

    /**
     * Store a new category
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'            => 'required|image',
            'name'             => 'required|string|max:255|unique:categories,name',
            'parent_id'        => 'nullable|exists:categories,id',
            'title_section'    => 'nullable|string|max:255',
            'sous_title_section' => 'nullable|string|max:1000',
            'is_active'        => 'required|boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Validation anti-boucle : vérifier que parent_id n'est pas un descendant (bien que pour une nouvelle catégorie ce soit impossible)
        if ($validated['parent_id']) {
            $parentCategory = Category::find($validated['parent_id']);
            if ($parentCategory && $parentCategory->isDescendantOf($validated['parent_id'])) {
                return response()->json([
                    'errors' => ['parent_id' => ['Boucle hiérarchique détectée.']]
                ], 422);
            }
        }

        // Image Upload
        $imagePath = null;

        if ($request->hasFile('image')) {
            $filename = Str::uuid() . '.webp';
            $path     = 'categories/' . $filename;

            $image = Image::read($request->file('image'))->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);

            $imagePath = $path;
        }

        $category = Category::create([
            'name'             => $validated['name'],
            'slug'             => Str::slug($validated['name']),
            'parent_id'        => $validated['parent_id'] ?? null,
            'image'            => $imagePath,
            'title_section'    => $validated['title_section'] ?? null,
            'sous_title_section' => $validated['sous_title_section'] ?? null,
            'is_active'        => (bool) $validated['is_active'],
            'meta_title'       => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords'    => $validated['meta_keywords'] ?? null,
        ]);

        return response()->json([
            'message'  => 'Catégorie créée avec succès',
            'category' => $category
        ], 201);
    }

    /**
     * Get category for edit
     */
    public function edit(Category $category)
    {
        // ajouter l’URL de l’image pour l’aperçu
        $category->image_url = $category->image ? asset('storage/' . $category->image) : null;
        return response()->json($category);
    }

    /**
     * Update category
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'image'            => 'nullable|image',
            'name'             => 'required|string|max:255|unique:categories,name,' . $category->id,
            'parent_id'        => 'nullable|exists:categories,id',
            'title_section'    => 'nullable|string|max:255',
            'sous_title_section' => 'nullable|string|max:1000',
            'is_active'        => 'required|boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Validation anti-boucle : vérifier que le nouveau parent_id ne crée pas de boucle
        if ($validated['parent_id']) {
            // Ne pas permettre de choisir soi-même comme parent
            if ($validated['parent_id'] == $category->id) {
                return response()->json([
                    'errors' => ['parent_id' => ['Une catégorie ne peut pas être son propre parent.']]
                ], 422);
            }

            // Vérifier si le parent choisi est un descendant de la catégorie actuelle
            if ($category->isDescendantOf($validated['parent_id'])) {
                return response()->json([
                    'errors' => ['parent_id' => ['Vous ne pouvez pas choisir une sous-catégorie comme parent de sa propre branche.']]
                ], 422);
            }
        }

        // Image Upload
        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $filename = Str::uuid() . '.webp';
            $path     = 'categories/' . $filename;

            $image = Image::read($request->file('image'))->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);

            $category->image = $path;
        }

        $category->update([
            'name'             => $validated['name'],
            'slug'             => Str::slug($validated['name']),
            'parent_id'        => $validated['parent_id'] ?? null,
            'title_section'    => $validated['title_section'] ?? null,
            'sous_title_section' => $validated['sous_title_section'] ?? null,
            'is_active'        => (bool) $validated['is_active'],
            'meta_title'       => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords'    => $validated['meta_keywords'] ?? null,
        ]);

        return response()->json([
            'message'  => 'Catégorie mise à jour avec succès',
            'category' => $category->fresh()
        ]);
    }

    /**
     * Delete category
     */
    public function destroy(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json([
            'message' => 'Catégorie supprimée avec succès'
        ]);
    }
    
    /**
     * Vérifier si un parent_id créerait une boucle (pour la création)
     */
    private function wouldCreateLoop($parentId)
    {
        // Pas de parent = pas de boucle possible
        if (!$parentId) {
            return false;
        }
        
        // Pour une nouvelle catégorie, on ne peut pas créer de boucle
        // Cette méthode pourrait être étendue si nécessaire
        return false;
    }
    
    /**
     * Vérifier si une catégorie est un descendant d'une autre
     */
    private function isDescendant($potentialChildId, $parentId)
    {
        $child = Category::find($potentialChildId);
        
        if (!$child) {
            return false;
        }
        
        // Parcourir récursivement les parents
        while ($child && $child->parent_id) {
            if ($child->parent_id == $parentId) {
                return true;
            }
            $child = $child->parent;
        }
        
        return false;
    }
    
    /**
     * Obtenir les catégories pour les formulaires (format hiérarchique)
     */
    public function getCategoryOptions()
    {
        try {
            $categories = Category::where('is_active', true)
                                ->orderBy('name')
                                ->get();
            
            $options = [];
            
            foreach ($categories as $category) {
                $options[$category->id] = $category->name;
            }
            
            return response()->json($options);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors du chargement des catégories: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Ajouter récursivement les options enfants avec indentation
     */
    private function addChildOptions($children, &$options, $level)
    {
        foreach ($children as $child) {
            $indent = str_repeat('—', $level) . ' ';
            $options[$child->id] = $indent . $child->name;
            
            // Récursif pour les sous-enfants
            if ($child->children->isNotEmpty()) {
                $this->addChildOptions($child->children, $options, $level + 1);
            }
        }
    }

    /**
     * Get hierarchical categories for AJAX requests
     */
    public function hierarchical(Request $request)
    {
        $excludeId = $request->get('exclude_id');
        $categories = Category::getHierarchicalList($excludeId);
        
        return response()->json($categories);
    }
}
