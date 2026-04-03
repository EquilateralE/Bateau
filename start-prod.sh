#!/bin/bash

# =============================================================
#  Script de démarrage — Environnement de PRODUCTION
#  Usage : ./start-prod.sh
# =============================================================

set -e

REPO_URL="https://github.com/TON_ORG/vide-grenier.git"   # 👈 Remplace par ton URL
BRANCH="main"
COMPOSE_FILE="docker-compose.prod.yml"

echo ""
echo "🏭 ===== VIDE GRENIER — Environnement PROD ====="
echo ""

# 1. Récupération du code (branche main/master)
if [ -d ".git" ]; then
    echo "📥 Mise à jour du dépôt git (branche $BRANCH)..."
    git fetch --all
    git checkout "$BRANCH"
    git pull origin "$BRANCH"
else
    echo "📥 Clonage du dépôt (branche $BRANCH)..."
    git clone --branch "$BRANCH" "$REPO_URL" .
fi

echo ""
echo "🔨 Build de l'image Docker web (code embarqué depuis $BRANCH)..."
docker compose -f "$COMPOSE_FILE" build --no-cache web

echo ""
echo "🐳 Démarrage des conteneurs PROD..."
docker compose -f "$COMPOSE_FILE" up -d

echo ""
echo "⏳ Attente de la base de données..."
sleep 5

echo ""
echo "✅ Environnement PROD prêt !"
echo "   → Application : http://localhost:8081"
echo "   → MySQL NON exposé (sécurité prod)"
echo "   → Code web : embarqué dans l'image Docker"
echo ""
