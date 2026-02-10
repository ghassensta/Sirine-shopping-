<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Blog;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Avis;

class AjaxController extends Controller
{
public function getCommandes(Request $request)
{
    $draw = $request->input('draw', 1);
    $start = $request->input('start', 0);
    $rowPerPage = $request->input('length', 10);
    $searchValue = '';
    $statut_id = $request->input('statut_id');

    // Récupération des paramètres de tri
    $order = $request->input('order', []);
    $columnIndex = isset($order[0]['column']) ? $order[0]['column'] : 0;
    $columnSortOrder = isset($order[0]['dir']) ? $order[0]['dir'] : 'desc';

    // Mapping des colonnes
    $columns = ['id', 'created_at', 'client_id', 'total_ttc', 'statut_id'];
    $orderColumn = $columns[$columnIndex] ?? 'created_at';

    // Récupération de la recherche
    $search = $request->input('search');
    if (is_array($search) && isset($search['value'])) {
        $searchValue = $search['value'];
    }

    // Construction de la requête optimisée
    $query = Order::select([
        'orders.id',
        'orders.numero_commande',
        'orders.created_at',
        'orders.client_id',
        'orders.statut_id',
        'orders.total_ttc',
        'orders.subtotal_ht',
        'orders.shipping_cost'
    ])
    ->with([
        'statut:id,name',
        'client:id,name,adresse,phone',
        'items' => function ($query) {
            $query->select('order_items.id', 'order_items.order_id', 'order_items.product_id', 'order_items.quantity', 'order_items.unit_price', 'order_items.subtotal')
                ->with(['product:id,name,images']);
        }
    ]);

    // Filtre de recherche
    if (!empty($searchValue)) {
        $query->where(function ($q) use ($searchValue) {
            $q->where('orders.numero_commande', 'like', '%' . $searchValue . '%')
              ->orWhereHas('client', function ($q) use ($searchValue) {
                  $q->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('phone', 'like', '%' . $searchValue . '%');
              });
        });
    }

    // Filtre par statut
    if ($statut_id && $statut_id !== 'all') {
        $query->where('statut_id', $statut_id);
    }

    // Total des enregistrements
    $totalRecords = Order::count();
    $totalFiltered = $query->count();

    // Application du tri et pagination
    $commandes = $query->orderBy($orderColumn, $columnSortOrder)
        ->offset($start)
        ->limit($rowPerPage)
        ->get();

    // Formatage des données pour DataTable
    $data_arr = $commandes->map(function ($row) {
        $clientImages = is_array($row->client->images ?? null) ? $row->client->images : [];
        $clientFirstImage = !empty($clientImages) ? $clientImages[0] : null;

        return [
            'id' => $row->id,
            'numero_commande' => $row->numero_commande,
            'date' => $row->created_at->format('d/m/Y'),
            'date_full' => $row->created_at->format('d/m/Y H:i'),
            'client_name' => optional($row->client)->name ?? 'N/A',
            'client_phone' => optional($row->client)->phone ?? 'N/A',
            'client_adresse' => optional($row->client)->adresse ?? 'N/A',
            'total_ttc' => number_format($row->total_ttc ?? 0, 3, '.', ''),
            'subtotal_ht' => number_format($row->subtotal_ht ?? 0, 3, '.', ''),
            'shipping_cost' => number_format($row->shipping_cost ?? 0, 3, '.', ''),
            'items_count' => $row->items->count(),
            'items' => $row->items->map(function($item) {
                $images = [];
                if ($item->product) {
                    $productImages = $item->product->images;
                    if (is_string($productImages)) {
                        $images = json_decode($productImages, true) ?? [];
                    } elseif (is_array($productImages)) {
                        $images = $productImages;
                    }
                }

                return [
                    'name' => optional($item->product)->name ?? 'Produit supprimé',
                    'image' => !empty($images) ? $images[0] : null,
                    'quantity' => $item->quantity,
                    'unit_price' => number_format($item->unit_price ?? 0, 3, '.', ''),
                    'subtotal' => number_format($item->subtotal ?? 0, 3, '.', ''),
                ];
            })->toArray(),
            'statut_name' => optional($row->statut)->name ?? 'N/A',
        ];
    })->toArray();

    return response()->json([
        'draw' => intval($draw),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalFiltered,
        'data' => $data_arr,
    ]);
}

    public function getProducts(Request $request)
    {
        // 1) Paramètres DataTables avec valeurs par défaut
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $rowPerPage = (int) $request->input('length', 10);
        $orderArr = $request->input('order', []);
        $columnsArr = $request->input('columns', []);
        $searchValue = $request->input('search.value', '');

        $columnIndex = $orderArr[0]['column'] ?? 0;
        $columnSortOrder = $orderArr[0]['dir'] ?? 'asc';
        $columnName = $columnsArr[$columnIndex]['data'] ?? 'name';

        // Colonnes triables
        $sortable = [
            'id' => 'products.id',
            'image_avant' => 'products.image_avant',
            'images' => 'products.created_at',
            'name' => 'products.name',
            'description' => 'products.description',
            'price' => 'products.price',
            'stock' => 'products.stock',
            'is_active' => 'products.is_active',
            'created_at' => 'products.created_at',
        ];
        $orderBy = $sortable[$columnName] ?? 'products.created_at';

        // 2) Requête filtrée avec chargement des catégories
        $query = Product::with(['categories' => function($q) {
            $q->select('categories.id', 'categories.name');
        }]);

        if (!empty($searchValue)) {
            $query->where('products.name', 'like', "%{$searchValue}%");
        }

        $totalRecords = $query->count();

        $products = $query->orderBy($orderBy, $columnSortOrder)
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        // 3) Formatage DataTables
        $data = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'image_avant' => $product->image_avant,
                'images' => $product->images ?? [],
                'categories' => $product->categories->pluck('name')->toArray(),
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'is_active' => $product->is_active,
                'meta_title' => $product->meta_title,
                'meta_description' => $product->meta_description,
                'meta_keywords' => $product->meta_keywords,
                'slug' => $product->slug,
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    public function getCategory(Request $request)
{
    $draw        = (int) $request->input('draw', 1);
    $start       = (int) $request->input('start', 0);
    $length      = (int) $request->input('length', 10);
    $searchValue = trim($request->input('search.value', ''));

    // Index de la colonne sur laquelle trier
    $order       = $request->input('order.0', []);
    $columnIndex = $order['column'] ?? 2; // par défaut : colonne "name"
    $dir         = $order['dir'] ?? 'asc';

    // Mapping colonnes DataTables → colonnes base de données
    $columns = $request->input('columns', []);
    $columnName = $columns[$columnIndex]['data'] ?? 'name';

    $sortable = [
        'id'          => 'id',
        'image'       => 'image',           // tri sur image inutile, mais accepté
        'name'        => 'name',            // on trie sur name (car hierarchical_name est un accessor)
        'parent_name' => 'parent_id',       // tri sur parent_id
        'is_publish'  => 'is_publish',
        'is_active'   => 'is_active',
        'order'       => 'order',
        'created_at'  => 'created_at',
    ];

    $orderBy = $sortable[$columnName] ?? 'name';

    // Requête de base
    $query = Category::query()
        ->select('categories.id', 'categories.parent_id', 'categories.image', 'categories.name', 'categories.slug',
                 'categories.is_publish', 'categories.order', 'categories.is_active', 'categories.created_at')
        ->with('parent:id,name');

    // Recherche globale
    if ($searchValue !== '') {
        $query->where(function ($q) use ($searchValue) {
            $q->where('name', 'like', "%{$searchValue}%")
              ->orWhereHas('parent', function ($sub) use ($searchValue) {
                  $sub->where('name', 'like', "%{$searchValue}%");
              });
        });
    }

    // Nombre total et filtré
    $totalRecords    = Category::count();
    $filteredRecords = $query->count();

    // Tri + pagination
    $categories = $query
        ->orderBy($orderBy, $dir)
        ->offset($start)
        ->limit($length)
        ->get();

    // Formatage des données pour DataTables
    $data = $categories->map(function ($cat) {
        return [
            'id'          => $cat->id,
            'image'       => $cat->image_url
                ? '<img src="' . e($cat->image_url) . '" width="50" alt="' . e($cat->name) . '" class="rounded">'
                : '<span class="text-muted">—</span>',
            'name'        => $cat->full_name ?? $cat->name,
            'parent_name' => $cat->parent ? e($cat->parent->name) : '—',
            'is_publish'  => $cat->is_publish
                ? '<span class="badge bg-label-success">Publiée</span>'
                : '<span class="badge bg-label-danger">Non publiée</span>',
            'is_active'   => $cat->is_active
                ? '<span class="badge bg-label-success">Active</span>'
                : '<span class="badge bg-label-warning">Inactive</span>',
            'order'       => $cat->order ?? '—',
            'created_at'  => $cat->created_at?->format('d/m/Y H:i') ?? '—',
        ];
    })->toArray();

    return response()->json([
        'draw'            => $draw,
        'recordsTotal'    => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data'            => $data,
    ]);
}
    public function getBlog(Request $request)
    {
        // DataTables parameters
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $rowPerPage = (int) $request->input('length', 10);
        $orderArr = $request->input('order', []);
        $columnsArr = $request->input('columns', []);
        $searchValue = $request->input('search.value', '');

        // Sorting column
        $columnIndex = $orderArr[0]['column'] ?? 0;
        $columnSortOrder = $orderArr[0]['dir'] ?? 'asc';
        $columnName = $columnsArr[$columnIndex]['data'] ?? 'title';

        // Map DataTables columns to database columns
        $sortable = [
            'id' => 'id',
            'image' => 'image',
            'title' => 'title',
            'resume' => 'resume',
            'description' => 'description',
            'is_active' => 'is_active',
            'created_at' => 'created_at',
        ];
        $orderBy = $sortable[$columnName] ?? 'created_at';

        // Build query
        $query = Blog::select(['id', 'slug', 'image', 'title', 'resume', 'description', 'is_active', 'created_at']);

        // Apply search filter
        if ($searchValue !== '') {
            $query->where(function ($q) use ($searchValue) {
                $q->where('title', 'like', "%{$searchValue}%")
                    ->orWhere('resume', 'like', "%{$searchValue}%")
                    ->orWhere('description', 'like', "%{$searchValue}%");
            });
        }

        // Total records before filtering
        $totalRecords = Blog::count();
        // Total records after filtering
        $filteredRecords = $query->count();

        // Fetch paginated data
        $blogs = $query->orderBy($orderBy, $columnSortOrder)
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        // Format data for DataTables
        $data = $blogs->map(fn($blog) => [
            'id' => $blog->id,
            'image' => $blog->image && Storage::disk('public')->exists($blog->image)
                ? '<img src="' . Storage::url($blog->image) . '" width="50" alt="' . e($blog->title) . '">'
                : '-',
            'title' => $blog->title,
            'resume' => $blog->resume ? Str::limit(strip_tags($blog->resume), 50) : '-',
            'description' => $blog->description ? Str::limit(strip_tags($blog->description), 50) : '-',
            'is_active' => '<label class="switch "><input type="checkbox" class="toggle-active" data-id="' . $blog->id . '" ' . ($blog->is_active ? 'checked' : '') . '><span class="slider round"></span></label>',
            'created_at' => $blog->created_at->format('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

  public function getAvis(Request $request)
    {
        $draw = $request->input('draw', 1);
        $start = $request->input('start', 0);
        $rowPerPage = $request->input('length', 10);
        $columnIndex = 0;
        $columnName = 'id';
        $columnSortOrder = 'asc';
        $searchValue = '';
        $product_id = $request->input('product_id');

        // Order information
        $order = $request->input('order');
        if (is_array($order) && !empty($order) && isset($order[0]['column']) && isset($order[0]['dir'])) {
            $columnIndex = $order[0]['column'];
            $columnSortOrder = $order[0]['dir'];
        }

        // Column name
        $columns = $request->input('columns');
        if (is_array($columns) && isset($columns[$columnIndex]['data'])) {
            $columnName = $columns[$columnIndex]['data'];
        }

        // Column mapping
        $columnMap = [
            'id' => 'id',
            'product_id' => 'product_id',
            'rating' => 'rating',
            'comment' => 'comment',
            'name' => 'name',
            'approved' => 'approved',
        ];

        $dbColumnName = $columnMap[$columnName] ?? 'id';

        // Search value
        $search = $request->input('search');
        if (is_array($search) && isset($search['value'])) {
            $searchValue = $search['value'];
        }

        // Build query
        $query = Avis::select([
            'avis.id',
            'avis.product_id',
            'avis.rating',
            'avis.comment',
            'avis.name',
            'avis.approved',
            'avis.created_at'
        ])
            ->with(['product:id,name'])
            ->orderByDesc('created_at');

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%')
                  ->orWhere('comment', 'like', '%' . $searchValue . '%')
                  ->orWhereHas('product', function ($q) use ($searchValue) {
                      $q->where('name', 'like', '%' . $searchValue . '%');
                  });
            });
        }

        // Apply product filter
        if ($product_id && $product_id !== 'all') {
            $query->where('product_id', $product_id);
        }

        // Get total records
        $totalRecords = $query->count();

        // Apply sorting and pagination
        $avis = $query->orderBy($dbColumnName, $columnSortOrder)
            ->offset($start)
            ->limit($rowPerPage)
            ->get();

        // Map data for DataTables
        $data_arr = $avis->map(function ($row) {
            return [
                'id' => $row->id,
                'date' => $row->created_at?->format('d-m-Y') ?? 'N/A',
                'product' => optional($row->product)->name ?? 'N/A',
                'rating' => $row->rating ?? 0,
                'comment' => $row->comment ?? 'N/A',
                'name' => $row->name ?? 'N/A',
                'approved' => $row->approved ? 'Approuvé' : 'En attente'
            ];
        })->toArray();

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data_arr,
        ]);
    }

}
