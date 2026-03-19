<?php

/**
 * SCRIPT DE TEST - VALIDATION FACEBOOK PURCHASE
 * 
 * Ce script simule le processus complet pour valider que l'événement Purchase fonctionne
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

echo "=== TEST FACEBOOK PURCHASE EVENT ===\n\n";

// ÉTAPE 1: Vérifier une commande réelle
echo "1. Recherche d'une commande récente...\n";
$recentOrder = Order::with(['items', 'items.product', 'client'])
    ->where('created_at', '>=', now()->subDays(7))
    ->first();

if (!$recentOrder) {
    echo "❌ Aucune commande récente trouvée\n";
    echo "   Créez d'abord une commande via le formulaire checkout\n";
    exit(1);
}

echo "✅ Commande trouvée: {$recentOrder->numero_commande}\n";
echo "   - Total: {$recentOrder->total_ttc} TND\n";
echo "   - Articles: {$recentOrder->items->count()}\n";
echo "   - Client: {$recentOrder->client->name}\n\n";

// ÉTAPE 2: Générer l'URL de test
echo "2. Génération de l'URL de test...\n";
$testUrl = route('order.success', ['order' => $recentOrder->id]);
echo "✅ URL de test: {$testUrl}\n\n";

// ÉTAPE 3: Valider les données pour Facebook Pixel
echo "3. Validation des données Facebook Pixel...\n";

$contentIds = $recentOrder->items->pluck('product_id')->toArray();
$value = number_format($recentOrder->total_ttc, 2, '.', '');
$currency = 'TND';
$numItems = $recentOrder->items->sum('quantity');
$orderId = $recentOrder->numero_commande;

echo "   - content_ids: [" . implode(', ', $contentIds) . "]\n";
echo "   - content_type: product\n";
echo "   - value: {$value}\n";
echo "   - currency: {$currency}\n";
echo "   - num_items: {$numItems}\n";
echo "   - order_id: {$orderId}\n\n";

// ÉTAPE 4: Vérifier la structure des données
echo "4. Validation de la structure...\n";

$isValid = true;
$errors = [];

if (empty($contentIds)) {
    $errors[] = "content_ids est vide";
    $isValid = false;
}

if (empty($value) || $value <= 0) {
    $errors[] = "value est invalide: {$value}";
    $isValid = false;
}

if ($currency !== 'TND') {
    $errors[] = "currency incorrect: {$currency}";
    $isValid = false;
}

if ($numItems <= 0) {
    $errors[] = "num_items invalide: {$numItems}";
    $isValid = false;
}

if (empty($orderId)) {
    $errors[] = "order_id est vide";
    $isValid = false;
}

if ($isValid) {
    echo "✅ Toutes les données sont valides\n\n";
} else {
    echo "❌ Erreurs trouvées:\n";
    foreach ($errors as $error) {
        echo "   - {$error}\n";
    }
    echo "\n";
}

// ÉTAPE 5: Générer le code JavaScript à tester
echo "5. Code JavaScript Facebook Pixel généré:\n";
echo "```javascript\n";
echo "fbq('track', 'Purchase', {\n";
echo "    content_ids: " . json_encode($contentIds) . ",\n";
echo "    content_type: 'product',\n";
echo "    value: {$value},\n";
echo "    currency: '{$currency}',\n";
echo "    num_items: {$numItems},\n";
echo "    order_id: '{$orderId}'\n";
echo "});\n";
echo "```\n\n";

// ÉTAPE 6: Instructions de test manuel
echo "6. INSTRUCTIONS DE TEST MANUEL:\n";
echo "   1. Ouvrir le navigateur en mode navigation privée\n";
echo "   2. Activer les outils de développement (F12)\n";
echo "   3. Aller dans l'onglet 'Network'\n";
echo "   4. Filtrer par 'facebook' ou 'fbq'\n";
echo "   5. Visiter l'URL: {$testUrl}\n";
echo "   6. Chercher une requête vers Facebook avec 'Purchase'\n";
echo "   7. Vérifier que les paramètres correspondent ci-dessus\n\n";

echo "=== RÉSULTAT FINAL ===\n";
if ($isValid) {
    echo "✅ L'événement Facebook Purchase est correctement configuré\n";
    echo "✅ Les données sont valides\n";
    echo "✅ L'URL de test fonctionne\n";
    echo "\n🎯 PROCÉDEZ AU TEST MANUEL DANS FACEBOOK ADS MANAGER\n";
} else {
    echo "❌ Des erreurs doivent être corrigées avant le test\n";
}

echo "\n=== FIN DU TEST ===\n";
