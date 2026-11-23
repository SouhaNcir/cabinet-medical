#!/usr/bin/env bash
# =============================================================================
# scripts/check-project.sh
# Vérifie que le projet Cabinet Médical est dans un état livrable.
#   - code retour 0 : tout est correct
#   - code retour 1 : au moins une vérification a échoué
# Usage : ./scripts/check-project.sh
# =============================================================================

# On n'utilise PAS "set -e" : on veut exécuter toutes les vérifications
# et afficher tous les problèmes, pas s'arrêter au premier.
set -u

# Se placer à la racine du projet, quel que soit l'endroit d'où on lance le script.
cd "$(dirname "${BASH_SOURCE[0]}")/.." || exit 2

ERRORS=0

section() { printf '\n== %s ==\n' "$1"; }
ok()      { printf '  [OK]    %s\n' "$1"; }
fail()    { printf '  [ECHEC] %s\n' "$1"; ERRORS=$((ERRORS + 1)); }

# -----------------------------------------------------------------------------
section "1. Outils nécessaires"
# -----------------------------------------------------------------------------
HAS_PHP=0
if command -v php >/dev/null 2>&1; then
    HAS_PHP=1
    ok "php disponible (version $(php -r 'echo PHP_VERSION;'))"
else
    fail "php est introuvable : impossible de vérifier la syntaxe"
fi

# -----------------------------------------------------------------------------
section "2. Fichiers indispensables"
# -----------------------------------------------------------------------------
REQUIRED_FILES=(
    "README.md"
    ".gitignore"
    ".env.example"
    "public/index.php"
    "public/.htaccess"
    "app/Config/config.php"
    "app/Core/App.php"
    "app/Core/Database.php"
    "app/Controllers/HealthController.php"
)

for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        ok "$file"
    else
        fail "fichier manquant : $file"
    fi
done

# -----------------------------------------------------------------------------
section "3. Syntaxe PHP"
# -----------------------------------------------------------------------------
if [ "$HAS_PHP" -eq 1 ]; then
    COUNT=0
    SYNTAX_ERRORS=0
    while IFS= read -r -d '' file; do
        COUNT=$((COUNT + 1))
        if ! RESULT=$(php -l "$file" 2>&1); then
            fail "erreur de syntaxe dans $file"
            SYNTAX_ERRORS=$((SYNTAX_ERRORS + 1))
            printf '%s\n' "$RESULT" | sed 's/^/            /'
        fi
    done < <(find app public -name '*.php' -print0)

    if [ "$COUNT" -eq 0 ]; then
        fail "aucun fichier PHP trouvé dans app/ et public/"
    elif [ "$SYNTAX_ERRORS" -eq 0 ]; then
        ok "$COUNT fichiers PHP analysés, aucune erreur de syntaxe"
    fi
else
    fail "vérification de syntaxe ignorée faute de php"
fi

# -----------------------------------------------------------------------------
section "4. Éléments essentiels du projet"
# -----------------------------------------------------------------------------

# 4.1 .env doit être ignoré par Git, et ne JAMAIS être suivi (il contient des secrets).
# On interroge Git lui-même (git check-ignore) plutôt que de chercher du texte dans
# .gitignore : c'est fiable même si le fichier a des fins de ligne Windows (CRLF).
if git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
    if git check-ignore -q .env; then
        ok ".env est ignoré par Git"
    else
        fail ".env n'est pas ignoré par Git (vérifier .gitignore)"
    fi

    if git ls-files --error-unmatch .env >/dev/null 2>&1; then
        fail ".env est suivi par Git : danger, il peut contenir des secrets"
    else
        ok ".env n'est pas suivi par Git"
    fi
else
    fail "ce dossier n'est pas un dépôt Git : impossible de vérifier .env"
fi

# 4.3 .env.example doit documenter toutes les variables utilisées
for key in APP_ENV BASE_URL DB_HOST DB_PORT DB_NAME DB_USER DB_PASS; do
    if grep -q "^${key}=" .env.example 2>/dev/null; then
        ok ".env.example définit $key"
    else
        fail ".env.example ne définit pas $key"
    fi
done

# 4.4 La route /health doit être reconnue par le routeur
if grep -q "health" app/Core/App.php 2>/dev/null; then
    ok "la route /health est déclarée dans app/Core/App.php"
else
    fail "la route /health n'est pas déclarée dans app/Core/App.php"
fi

# -----------------------------------------------------------------------------
# (Étape D : les tests automatisés seront ajoutés ici.)
# -----------------------------------------------------------------------------

# -----------------------------------------------------------------------------
section "Résultat"
# -----------------------------------------------------------------------------
if [ "$ERRORS" -eq 0 ]; then
    echo "  SUCCES : toutes les vérifications sont passées."
    exit 0
else
    echo "  ECHEC : $ERRORS vérification(s) en échec."
    exit 1
fi
