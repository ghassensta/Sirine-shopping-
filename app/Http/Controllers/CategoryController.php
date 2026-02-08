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
    public function index()
    {
        return view('admin.category.index');
    }

    /**
     * Données pour DataTables (server-side)
     */
    public function get(Request $request)
    {
        $query = Category::with('parent')->select('categories.*');

        return DataTables::of($query)
            ->addColumn('image', function ($row) {
                $url = $row->image_url;
                return '<img src="' . e($url) . '" alt="' . e($row->name) . '" width="50" class="rounded">';
            })
            ->addColumn('hierarchical_name', function ($row) {
                return $row->hierarchical_name;
            })
            ->addColumn('parent_name', function ($row) {
                return $row->parent_name;
            })
            ->addColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-label-success">Active</span>'
                    : '<span class="badge bg-label-warning">Inactive</span>';
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y H:i');
            })
            ->addColumn('actions', function ($row) {
                return '
                    <div class="d-inline-flex">
                        <a href="javascript:void(0)" class="text-body edit-category me-3" data-id="' . $row->id . '">
                            <i class="ti ti-edit ti-sm"></i>
                        </a>
                        <a href="javascript:void(0)" class="text-body delete-category" data-id="' . $row->id . '">
                            <i class="ti ti-trash ti-sm"></i>
                        </a>
                    </div>';
            })
            ->rawColumns(['image', 'is_active', 'actions'])
            ->make(true);
    }

    /**
     * Afficher une catégorie (pour édition AJAX)
     */
    public function show(Category $category)
    {
        return response()->json([
            'id'                 => $category->id,
            'name'               => $category->name,
            'parent_id'          => $category->parent_id,
            'title_section'      => $category->title_section,
            'sous_title_section' => $category->sous_title_section,
            'order'              => $category->order,
            'is_publish'         => $category->is_publish,
            'is_active'          => $category->is_active,
            'meta_title'         => $category->meta_title,
            'meta_description'   => $category->meta_description,
            'meta_keywords'      => $category->meta_keywords,
            'image_url'          => $category->image_url,
        ]);
    }

    /**
     * Créer une nouvelle catégorie
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'               => 'required|string|max:255|unique:categories,name',
            'parent_id'          => 'nullable|exists:categories,id',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title_section'      => 'nullable|string|max:255',
            'sous_title_section' => 'nullable|string|max:500',
            'order'              => 'nullable|integer|min:0',
            'is_publish'         => 'nullable|boolean',
            'is_active'          => 'required|boolean',
            'meta_title'         => 'nullable|string|max:255',
            'meta_description'   => 'nullable|string|max:500',
            'meta_keywords'      => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Gestion image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $filename = Str::uuid() . '.webp';
            $path = 'categories/' . $filename;

            $img = Image::read($request->file('image'))->toWebp(82);
            Storage::disk('public')->put($path, (string) $img);

            $imagePath = $path;
        }

        $category = Category::create([
            'name'               => $data['name'],
            'slug'               => Str::slug($data['name']),
            'parent_id'          => $data['parent_id'] ?? null,
            'image'              => $imagePath,
            'title_section'      => $data['title_section'] ?? null,
            'sous_title_section' => $data['sous_title_section'] ?? null,
            'order'              => $data['order'] ?? 999,
            'is_publish'         => $data['is_publish'] ?? true,
            'is_active'          => $data['is_active'],
            'meta_title'         => $data['meta_title'] ?? $data['name'],
            'meta_description'   => $data['meta_description'] ?? null,
            'meta_keywords'      => $data['meta_keywords'] ?? null,
        ]);

        return response()->json(['message' => 'Catégorie créée', 'category' => $category], 201);
    }

    /**
     * Mettre à jour une catégorie
     */
   public function update(Request $request, Category $category)
{
    $validator = Validator::make($request->all(), [
        'name'               => 'required|string|max:255|unique:categories,name,' . $category->id,
        'parent_id'          => 'nullable|exists:categories,id',
        'image'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'title_section'      => 'nullable|string|max:255',
        'sous_title_section' => 'nullable|string|max:500',
        'order'              => 'nullable|integer|min:0',
        'is_publish'         => 'nullable|boolean',
        'is_active'          => 'required|boolean',
        'meta_title'         => 'nullable|string|max:255',
        'meta_description'   => 'nullable|string|max:500',
        'meta_keywords'      => 'nullable|string|max:500',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $data = $validator->validated();

    // Anti-boucle parent : VÉRIFICATION CORRECTE
    if ($data['parent_id']) {
        // 1. Ne pas se choisir soi-même comme parent
        if ($data['parent_id'] == $category->id) {
            return response()->json([
                'errors' => ['parent_id' => ['Impossible de se choisir soi-même comme parent.']]
            ], 422);
        }

        // 2. Vérifier si le nouveau parent est un descendant de la catégorie actuelle
        $newParent = Category::find($data['parent_id']);
        if ($newParent && $newParent->isDescendantOf($category->id)) {
            return response()->json([
                'errors' => ['parent_id' => ['Boucle hiérarchique détectée : le parent choisi est déjà un descendant de cette catégorie.']]
            ], 422);
        }
    }

    // Gestion de l'image
    if ($request->hasFile('image')) {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $filename = Str::uuid() . '.webp';
        $path = 'categories/' . $filename;

        $img = Image::read($request->file('image'))->toWebp(82);
        Storage::disk('public')->put($path, (string) $img);

        $category->image = $path;
    }

    // Mise à jour des champs
    $category->update([
        'name'               => $data['name'],
        'slug'               => Str::slug($data['name']),
        'parent_id'          => $data['parent_id'] ?? null,
        'title_section'      => $data['title_section'] ?? null,
        'sous_title_section' => $data['sous_title_section'] ?? null,
        'order'              => $data['order'] ?? 999,
        'is_publish'         => $data['is_publish'] ?? true,
        'is_active'          => $data['is_active'],
        'meta_title'         => $data['meta_title'] ?? $data['name'],
        'meta_description'   => $data['meta_description'] ?? null,
        'meta_keywords'      => $data['meta_keywords'] ?? null,
    ]);

    return response()->json([
        'message'  => 'Catégorie mise à jour avec succès',
        'category' => $category->fresh()
    ]);
}

    /**
     * Supprimer une catégorie
     */
    public function destroy(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json(['message' => 'Catégorie supprimée']);
    }

    /**
     * Liste hiérarchique pour les selects (AJAX)
     */
    public function hierarchical(Request $request)
    {
        $excludeId = $request->query('exclude_id');
        $list = Category::getHierarchicalList($excludeId);

        return response()->json($list);
    }
}