#!/usr/bin/env bash
# =============================================================================
# scripts/deploy.sh
# Met en service une version PUBLIÉE de l'image, sans jamais la reconstruire.
# Sert aussi au rollback : redéployer une version précédente.
#
# Usage : ./scripts/deploy.sh v1.0.0
# Prérequis : fichier .env présent (voir .env.example), Docker Compose installé,
#             et "docker login ghcr.io" si l'image est privée.
# Les volumes Docker (donc les données MySQL) ne sont JAMAIS supprimés ici.
# =============================================================================
set -euo pipefail

cd "$(dirname "${BASH_SOURCE[0]}")/.."

TAG="${1:-}"

if [[ -z "$TAG" ]]; then
    echo "Usage : $0 vX.Y.Z   (exemple : $0 v1.0.0)" >&2
    exit 2
fi

if [[ ! "$TAG" =~ ^v[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
    echo "Version invalide : '$TAG' (format attendu : vX.Y.Z)" >&2
    exit 2
fi

if [[ ! -f .env ]]; then
    echo "Fichier .env manquant : copier .env.example vers .env puis le remplir" >&2
    exit 2
fi

export IMAGE_TAG="$TAG"

echo "==> Téléchargement de l'image publiée ($TAG)"
docker compose pull app

echo "==> Mise en service sans reconstruction (--no-build)"
docker compose up -d --no-build

echo "==> Contrôle de santé"
bash scripts/wait-healthy.sh

cid="$(docker compose ps -q app)"
image="$(docker inspect "$cid" --format '{{.Config.Image}}')"
echo "==> Version en service"
echo "    Image   : $image"
echo "    Version : ${image##*:}"
echo "    Digest  : $(docker image inspect "$image" --format '{{join .RepoDigests ", "}}' 2>/dev/null || echo inconnu)"
