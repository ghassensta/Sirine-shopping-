#!/bin/bash

# Script de monitoring SEO - Vérifie les URLs du sitemap
# À exécuter régulièrement pour détecter les 404

echo "🔍 Monitoring SEO - Vérification des URLs du sitemap"
echo "Date: $(date)"
echo "========================================"

# Couleurs pour les logs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Compteur d'erreurs
ERROR_COUNT=0

# Fonction pour tester une URL
test_url() {
    local url=$1
    local expected_code=${2:-200}
    local http_code=$(curl -s -o /dev/null -w "%{http_code}" "$url")

    if [ "$http_code" -eq "$expected_code" ]; then
        echo -e "${GREEN}✅ $url${NC}"
    else
        echo -e "${RED}❌ $url (HTTP $http_code, attendu $expected_code)${NC}"
        ((ERROR_COUNT++))
    fi
}

echo "Testing sitemap URLs..."
echo ""

# Tester les sitemaps principaux
test_url "https://sirine-shopping.tn/sitemap.xml" 200
test_url "https://sirine-shopping.tn/robots.txt" 200

# Tester quelques URLs de produits depuis le sitemap
echo ""
echo "Testing sample product URLs..."

# Récupérer quelques URLs depuis le sitemap products (si disponible)
PRODUCT_URLS=$(curl -s "https://sirine-shopping.tn/sitemap-products.xml" | grep -o '<loc>[^<]*</loc>' | sed 's/<loc>//g' | sed 's/<\/loc>//g' | head -5)

if [ -n "$PRODUCT_URLS" ]; then
    echo "$PRODUCT_URLS" | while read -r url; do
        test_url "$url" 200
    done
else
    echo "⚠️ Impossible de récupérer les URLs du sitemap products"
fi

echo ""
echo "========================================"

if [ $ERROR_COUNT -eq 0 ]; then
    echo -e "${GREEN}✅ Toutes les URLs sont accessibles !${NC}"
    exit 0
else
    echo -e "${RED}❌ $ERROR_COUNT URL(s) en erreur${NC}"
    echo -e "${YELLOW}💡 Vérifiez les logs du serveur et relancez le déploiement${NC}"
    exit 1
fi
