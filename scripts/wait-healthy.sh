#!/usr/bin/env bash
# =============================================================================
# scripts/wait-healthy.sh
# Attend que l'application réponde HTTP 200 sur /health.
#   - code retour 0 : l'application est saine
#   - code retour 1 : toujours pas saine après toutes les tentatives
# Usage : ./scripts/wait-healthy.sh [URL] [TENTATIVES] [SECONDES_ENTRE_TENTATIVES]
# =============================================================================
set -u

URL="${1:-http://localhost:8080/health}"
TRIES="${2:-24}"
DELAY="${3:-5}"

for i in $(seq 1 "$TRIES"); do
    code="$(curl -s -o /dev/null -w '%{http_code}' --max-time 5 "$URL" || true)"
    if [ "$code" = "200" ]; then
        echo "[OK] $URL répond HTTP 200 (tentative $i/$TRIES)"
        exit 0
    fi
    echo "[..] tentative $i/$TRIES : HTTP ${code:-000}, nouvel essai dans ${DELAY}s"
    sleep "$DELAY"
done

echo "[ECHEC] $URL ne répond pas HTTP 200 après $TRIES tentatives" >&2
echo "--- état des conteneurs ---" >&2
docker compose ps -a >&2 || true
echo "--- derniers journaux ---" >&2
docker compose logs --tail=50 app db >&2 || true
exit 1
