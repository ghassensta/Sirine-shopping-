#!/bin/bash

# Script de déploiement Laravel avec corrections SEO
# À exécuter sur le serveur de production après chaque déploiement

echo "🚀 Début du déploiement Laravel avec corrections SEO..."

# 1. Nettoyer les caches
echo "🧹 Nettoyage des caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# 2. Générer les caches optimisés pour la production
echo "⚡ Génération des caches optimisés..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Définir les permissions correctes
echo "🔐 Définition des permissions..."
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# 4. Générer le sitemap (optionnel - si vous voulez le faire automatiquement)
# echo "🗺️ Génération du sitemap..."
# curl -s https://sirine-shopping.tn/sitemap.xml > /dev/null

# 5. Tester les URLs critiques
echo "🧪 Test des URLs critiques..."
echo "Testing sitemap.xml..."
curl -s -o /dev/null -w "sitemap.xml: %{http_code}\n" https://sirine-shopping.tn/sitemap.xml

echo "Testing robots.txt..."
curl -s -o /dev/null -w "robots.txt: %{http_code}\n" https://sirine-shopping.tn/robots.txt

echo "Testing homepage..."
curl -s -o /dev/null -w "homepage: %{http_code}\n" https://sirine-shopping.tn/

echo "✅ Déploiement terminé avec succès!"
echo "🔍 Vérifiez maintenant dans Google Search Console que les pages sont crawlées."
