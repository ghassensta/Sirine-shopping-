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
            return response()->json(['reply' => 'Service non configuré. Contactez-nous au +216 26 686 286.'], 503);
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
            ->get(['id', 'name', 'slug', 'price', 'price_baree', 'stock', 'description']);

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
        // 3. Formater le contexte
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
            return "• {$p->name} | {$prix}{$baree}{$stock} | {$cats} | {$siteUrl}/article/{$p->slug}";
        })->implode("\n");

        $promoList = $promos->take(10)->map(function ($p) use ($siteUrl, $currency) {
            $pct = round((1 - $p->price / $p->price_baree) * 100);
            return "• {$p->name} → -{$pct}% = " . number_format($p->price, 2) . " {$currency} | {$siteUrl}/article/{$p->slug}";
        })->implode("\n") ?: 'Aucune promo active.';

        // ═══════════════════════════════════════════
        // 4. System prompt avec vraies données
        // ═══════════════════════════════════════════

        $systemPrompt = <<<PROMPT
Tu es Sirine, conseillère shopping de {$siteName} ({$siteUrl}).
Tu connais EN TEMPS RÉEL tous les produits, prix et stocks exacts.

══ BOUTIQUE ══
- Livraison : {$shipping} {$currency} partout en Tunisie en {$delivery}
- Paiement : à la livraison (pas de carte requise)
- Contact : +216 26 686 286

══ CATÉGORIES ══
{$catList}

══ PRODUITS DISPONIBLES ══
{$prodList}

══ PROMOTIONS ══
{$promoList}

══ RÈGLES ══
1. Français chaleureux, max 3 phrases courtes.
2. Toujours donner le PRIX EXACT et le LIEN du produit mentionné.
3. Pour stock limité : crée l'urgence ("Plus que X en stock !").
4. Toujours suggérer un produit complémentaire.
5. Si le client écrit en arabe, réponds en arabe tunisien.
6. Jamais de produit en rupture de stock.
PROMPT;

        // ═══════════════════════════════════════════
        // 5. Appel Groq
        // ═══════════════════════════════════════════

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])
            ->timeout(20)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'llama-3.1-8b-instant',
                'max_tokens'  => 500,
                'temperature' => 0.7,
                'messages'    => array_merge(
                    [['role' => 'system', 'content' => $systemPrompt]],
                    $request->input('messages')
                ),
            ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content');
                if ($reply) {
                    return response()->json(['reply' => trim($reply)]);
                }
            }

            \Log::error('Groq error', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json(['reply' => "Service indisponible. Appelez-nous au **+216 26 686 286**. 💛"], 503);

        } catch (\Exception $e) {
            \Log::error('Chat exception: ' . $e->getMessage());
            return response()->json(['reply' => "Une erreur est survenue. Veuillez réessayer. 😊"], 500);
        }
    }
}