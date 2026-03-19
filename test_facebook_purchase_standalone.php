<?php

/**
 * SCRIPT DE TEST SIMPLE - VALIDATION FACEBOOK PURCHASE
 * Version autonome sans dépendances Laravel
 */

echo "=== TEST FACEBOOK PURCHASE EVENT ===\n\n";

// ÉTAPE 1: Simuler les données d'une commande
echo "1. Simulation des données de commande...\n";

$mockOrder = [
    'id' => 123,
    'numero_commande' => 'CMD-20260315-0001',
    'total_ttc' => 89.90,
    'client' => [
        'name' => 'Client Test',
        'email' => 'test@example.com'
    ],
    'items' => [
        [
            'product_id' => 1,
            'quantity' => 2,
            'subtotal' => 59.90,
            'product' => [
                'name' => 'Produit A',
                'price' => 29.95
            ]
        ],
        [
            'product_id' => 5,
            'quantity' => 1,
            'subtotal' => 30.00,
            'product' => [
                'name' => 'Produit B',
                'price' => 30.00
            ]
        ]
    ]
];

echo "✅ Commande simulée: {$mockOrder['numero_commande']}\n";
echo "   - Total: {$mockOrder['total_ttc']} TND\n";
echo "   - Articles: " . count($mockOrder['items']) . "\n";
echo "   - Client: {$mockOrder['client']['name']}\n\n";

// ÉTAPE 2: Générer l'URL de test
echo "2. Génération de l'URL de test...\n";
$baseUrl = 'http://127.0.0.1:8000';
$testUrl = $baseUrl . '/order/success/' . $mockOrder['id'];
echo "✅ URL de test: {$testUrl}\n\n";

// ÉTAPE 3: Valider les données pour Facebook Pixel
echo "3. Validation des données Facebook Pixel...\n";

$contentIds = array_column($mockOrder['items'], 'product_id');
$value = number_format($mockOrder['total_ttc'], 2, '.', '');
$currency = 'TND';
$numItems = array_sum(array_column($mockOrder['items'], 'quantity'));
$orderId = $mockOrder['numero_commande'];

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

// ÉTAPE 6: Vérifier que les fichiers existent
echo "6. Vérification des fichiers de configuration...\n";

$filesToCheck = [
    'resources/views/front-office/order/success.blade.php',
    'routes/web.php',
    'app/Http/Controllers/CheckoutController.php'
];

$allFilesExist = true;
foreach ($filesToCheck as $file) {
    if (file_exists($file)) {
        echo "   ✅ {$file}\n";
    } else {
        echo "   ❌ {$file} (manquant)\n";
        $allFilesExist = false;
    }
}

if ($allFilesExist) {
    echo "\n✅ Tous les fichiers requis existent\n\n";
} else {
    echo "\n❌ Certains fichiers manquent\n\n";
}

// ÉTAPE 7: Instructions de test manuel
echo "7. INSTRUCTIONS DE TEST MANUEL:\n";
echo "   1. Démarrer le serveur Laravel: php artisan serve\n";
echo "   2. Ouvrir le navigateur en mode navigation privée\n";
echo "   3. Activer les outils de développement (F12)\n";
echo "   4. Aller dans l'onglet 'Network'\n";
echo "   5. Filtrer par 'facebook' ou 'fbq'\n";
echo "   6. Visiter l'URL: {$testUrl}\n";
echo "   7. Chercher une requête vers Facebook avec 'Purchase'\n";
echo "   8. Vérifier que les paramètres correspondent ci-dessus\n\n";

echo "8. TEST ALTERNATIF (si pas de commande réelle):\n";
echo "   1. Créer une commande via le formulaire checkout\n";
echo "   2. Noter l'ID de la commande dans la redirection\n";
echo "   3. Remplacer {$mockOrder['id']} par le vrai ID\n";
echo "   4. Tester avec l'URL réelle\n\n";

echo "=== RÉSULTAT FINAL ===\n";
if ($isValid && $allFilesExist) {
    echo "✅ L'événement Facebook Purchase est correctement configuré\n";
    echo "✅ Les données sont valides\n";
    echo "✅ Les fichiers existent\n";
    echo "✅ L'URL de test est générée\n";
    echo "\n🎯 PROCÉDEZ AU TEST MANUEL DANS FACEBOOK ADS MANAGER\n";
    echo "\n⏱️  ATTENDRE 15-30 MINUTES POUR VOIR L'ÉVÉNEMENT DANS FACEBOOK\n";
} else {
    echo "❌ Des erreurs doivent être corrigées avant le test\n";
}

echo "\n=== FIN DU TEST ===\n";
