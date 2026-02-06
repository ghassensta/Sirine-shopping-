<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $blogs = Blog::latest()->paginate(10);
        return view('admin.blogs.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.blogs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'resume' => 'nullable|string',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'image' => 'nullable|image|',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['title', 'slug', 'resume', 'description', 'meta_title', 'meta_description', 'is_active']);

        // Générer le slug si non fourni
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        // Vérifier l'unicité du slug
        $baseSlug = $data['slug'];
        $counter = 1;
        while (Blog::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $counter++;
        }

        // Gestion de l'image
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $filename = Str::slug($data['title']) . '-' . time() . '.webp';
            $path = 'blogs/' . $filename;

            // Convertir en WebP avec Intervention Image
            $image = Image::read($request->file('image'))->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);

            // Stocke le chemin relatif
            $data['image'] = $path;
        }

        Blog::create($data);

        return redirect()->route('blogs.index')->with('message', 'Blog article created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $blog = Blog::where('id', $id)->firstOrFail();
        return view('admin.blogs.edit', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'resume' => 'nullable|string',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string|',
            'image' => 'nullable|image',
            'is_active' => 'boolean',
        ]);

        $blog = Blog::where('id', $id)->firstOrFail();
        $data = $request->only(['title', 'slug', 'resume', 'description', 'meta_title', 'meta_description', 'is_active']);

        // Générer le slug si non fourni
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        // Vérifier l'unicité du slug (sauf pour le blog actuel)
        $baseSlug = $data['slug'];
        $counter = 1;
        while (Blog::where('slug', $data['slug'])->where('id', '!=', $id)->exists()) {
            $data['slug'] = $baseSlug . '-' . $counter++;
        }

        // Gestion de l'image
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Supprimer l'ancienne image si elle existe
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }

            $filename = Str::slug($data['title']) . '-' . time() . '.webp';
            $path = 'blogs/' . $filename;

            // Convertir en WebP avec Intervention Image
            $image = Image::read($request->file('image'))->toWebp(80);
            Storage::disk('public')->put($path, (string) $image);

            // Stocke le chemin relatif
            $data['image'] = $path;
        }

        $blog->update($data);

        return redirect()->route('blogs.index')->with('message', 'Blog article updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blog = Blog::findOrFail($id);
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }
        $blog->delete();

        return response()->json([
            'message' => 'Blog article deleted successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->is_active = !$blog->is_active;
        $blog->save();

        return response()->json([
            'success' => true,
            'message' => 'Blog status updated successfully',
            'is_active' => $blog->is_active
        ]);
    }
}
