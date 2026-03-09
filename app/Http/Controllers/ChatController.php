<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use App\Models\Product;
use App\Models\Category;
use App\Models\Configuration;

class ChatController extends Controller
{
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'messages'           => 'required|array|min:1|max:30',
            'messages.*.role'    => 'required|in:user,assistant',
            'messages.*.content' => 'required|string|max:1000',
        ]);

        $apiKey = env('GROQ_API_KEY');
        if (!$apiKey) {
            return response()->json(['reply' => 'Service non configuré. Contactez-nous au +216 26 868 286.'], 503);
        }

        // ═══════════════════════════════════════════
        // 1. Données réelles depuis la BDD
        // ═══════════════════════════════════════════

        $config = Configuration::first();

        $categories = Category::active()
            ->whereNull('parent_id')
            ->with(['children' => fn($q) => $q->where('is_active', true)])
            ->get(['id', 'name', 'slug']);

        $products = Product::active()
            ->with('categories:id,name,slug')
            ->get(['id', 'name', 'slug', 'price', 'price_baree', 'stock', 'description', 'image_avant']);

        $promos   = $products->filter(fn($p) => $p->price_baree && $p->price_baree > $p->price);
        $lowStock = $products->filter(fn($p) => $p->stock > 0 && $p->stock <= 5);

        // ═══════════════════════════════════════════
        // 2. Config site
        // ═══════════════════════════════════════════

        $siteName     = $config?->site_name     ?? 'Sirine Shopping';
        $siteUrl      = 'https://sirine-shopping.tn';
        $shipping     = $config?->shipping_cost ?? 8;
        $delivery     = $config?->delivery_estimate_days ?? '24-48h';
        $currency     = 'DT';
        $supportEmail = $config?->support_email ?? 'support@sirine-shopping.tn';

        // ═══════════════════════════════════════════
        // 3. Catalogue JSON structuré (pour les product cards)
        // ═══════════════════════════════════════════

        $catalog = $products->where('stock', '>', 0)->take(60)->map(function ($p) use ($siteUrl, $currency) {
            $imageUrl = $p->image_avant
                ? asset('storage/' . $p->image_avant)
                : asset('images/no-image.png');

            return [
                'id'        => $p->id,
                'name'      => $p->name,
                'slug'      => $p->slug,
                'price'     => number_format($p->price, 2),
                'price_baree' => $p->price_baree && $p->price_baree > $p->price
                    ? number_format($p->price_baree, 2) : null,
                'discount'  => $p->price_baree && $p->price_baree > $p->price
                    ? round((1 - $p->price / $p->price_baree) * 100) : null,
                'stock'     => $p->stock,
                'low_stock' => $p->stock <= 5,
                'image'     => $imageUrl,
                'url'       => $siteUrl . '/article/' . $p->slug,
                'categories'=> $p->categories->pluck('name')->implode(', '),
            ];
        })->values()->toArray();

        // ═══════════════════════════════════════════
        // 4. Formater le contexte textuel
        // ═══════════════════════════════════════════

        $catList = $categories->map(function ($cat) use ($siteUrl) {
            $children = $cat->children->pluck('name')->implode(', ');
            $line = "- {$cat->name} → {$siteUrl}/collections/{$cat->slug}";
            if ($children) $line .= " (sous-catég.: {$children})";
            return $line;
        })->implode("\n");

        $prodList = $products->where('stock', '>', 0)->take(50)->map(function ($p) use ($siteUrl, $currency) {
            $prix  = number_format($p->price, 2) . " {$currency}";
            $baree = $p->price_baree && $p->price_baree > $p->price
                   ? " (était " . number_format($p->price_baree, 2) . " {$currency})" : '';
            $stock = $p->stock <= 5 ? " ⚠️ Stock limité: {$p->stock} restants" : '';
            $cats  = $p->categories->pluck('name')->implode(', ');
            return "• [{$p->id}] {$p->name} | {$prix}{$baree}{$stock} | {$cats}";
        })->implode("\n");

        $promoList = $promos->take(10)->map(function ($p) use ($currency) {
            $pct = round((1 - $p->price / $p->price_baree) * 100);
            return "• [{$p->id}] {$p->name} → -{$pct}% = " . number_format($p->price, 2) . " {$currency}";
        })->implode("\n") ?: 'Aucune promo active.';

        // ═══════════════════════════════════════════
        // 5. System prompt
        // ═══════════════════════════════════════════

        $systemPrompt = <<<PROMPT
Tu es Sirine, conseillère shopping de {$siteName} ({$siteUrl}).
Tu connais EN TEMPS RÉEL tous les produits, prix et stocks exacts.

══ BOUTIQUE ══
- Livraison : {$shipping} {$currency} partout en Tunisie en {$delivery}
- Paiement : à la livraison (pas de carte requise)
- Contact : +216 26 868 286

══ CATÉGORIES ══
{$catList}

══ PRODUITS DISPONIBLES (avec leur ID entre crochets) ══
{$prodList}

══ PROMOTIONS ══
{$promoList}

══ RÈGLES CRITIQUES ══
1. Français chaleureux, max 3 phrases courtes.
2. Quand tu mentionnes un produit, OBLIGATOIREMENT inclure son ID dans ce format exact : [[PRODUCT:ID]] — exemple : [[PRODUCT:42]]
3. Tu peux mentionner 1 à 3 produits maximum par réponse.
4. Pour stock limité : crée l'urgence ("Plus que X en stock !").
5. Toujours suggérer un produit complémentaire avec [[PRODUCT:ID]].
6. Si le client écrit en arabe, réponds en arabe tunisien mais garde le format [[PRODUCT:ID]].
7. Jamais de produit en rupture de stock.
8. Ne jamais inclure de liens URL dans ta réponse — les liens sont générés automatiquement.
PROMPT;

        // ═══════════════════════════════════════════
        // 6. Appel Groq
        // ═══════════════════════════════════════════

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])
            ->timeout(20)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'llama-3.3-70b-versatile',
                'max_tokens'  => 600,
                'temperature' => 0.7,
                'messages'    => array_merge(
                    [['role' => 'system', 'content' => $systemPrompt]],
                    $request->input('messages')
                ),
            ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content');
                if ($reply) {
                    // Extraire les IDs produits mentionnés
                    $mentionedIds = [];
                    preg_match_all('/\[\[PRODUCT:(\d+)\]\]/', $reply, $matches);
                    if (!empty($matches[1])) {
                        $mentionedIds = array_unique($matches[1]);
                    }

                    // Récupérer les cartes produits correspondantes
                    $productCards = [];
                    if (!empty($mentionedIds)) {
                        $productCards = collect($catalog)
                            ->whereIn('id', $mentionedIds)
                            ->values()
                            ->toArray();
                    }

                    // Nettoyer la réponse des tags [[PRODUCT:X]]
                    $cleanReply = preg_replace('/\[\[PRODUCT:\d+\]\]/', '', $reply);
                    $cleanReply = trim(preg_replace('/\s+/', ' ', $cleanReply));

                    return response()->json([
                        'reply'        => $cleanReply,
                        'products'     => $productCards,
                        'currency'     => $currency,
                    ]);
                }
            }

            \Log::error('Groq error', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json(['reply' => "Service indisponible. Appelez-nous au **+216 26 868 286**. 💛"], 503);

        } catch (\Exception $e) {
            \Log::error('Chat exception: ' . $e->getMessage());
            return response()->json(['reply' => "Une erreur est survenue. Veuillez réessayer. 😊"], 500);
        }
    }
}
