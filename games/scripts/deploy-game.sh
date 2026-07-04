#!/usr/bin/env bash
# Deploy Unity WebGL build for a game with versioning.
# Usage: deploy-game.sh <game-name> <version> [build-source-dir]
#
# Default source (if 3rd arg omitted):
#   /opt/games/<game-name>/upload/   (rsync/scp WebGL build here first)
#
# Example:
#   deploy-game.sh space-shooter v1.0
#   deploy-game.sh space-shooter v1.0 /path/to/WebGL/build/output
#
set -euo pipefail

GAMES_ROOT="${GAMES_ROOT:-/opt/games}"
NGINX_CONTAINER="${NGINX_CONTAINER_NAME:-nginx_proxy}"
RELOAD_NGINX="${RELOAD_NGINX:-1}"

GAME="${1:?game-name required}"
VERSION="${2:?version required (e.g. v1.0)}"
BUILD_SRC="${3:-}"

# Safe slug
if [[ ! "$GAME" =~ ^[a-zA-Z0-9_-]+$ ]]; then
  echo "ERROR: game-name must match [a-zA-Z0-9_-]+" >&2
  exit 1
fi
if [[ ! "$VERSION" =~ ^[a-zA-Z0-9._-]+$ ]]; then
  echo "ERROR: version must match [a-zA-Z0-9._-]+" >&2
  exit 1
fi

GAME_DIR="$GAMES_ROOT/$GAME"
BUILDS_DIR="$GAME_DIR/builds"
VERSION_DIR="$BUILDS_DIR/$VERSION"
WEB_LINK="$GAME_DIR/web"

if [[ ! -d "$GAME_DIR" ]]; then
  echo "ERROR: game directory not found: $GAME_DIR" >&2
  echo "Run: $GAMES_ROOT/scripts/init-game.sh $GAME" >&2
  exit 1
fi

UPLOAD_DIR="$GAME_DIR/upload"
mkdir -p "$BUILDS_DIR" "$GAME_DIR/android" "$UPLOAD_DIR"

if [[ -z "$BUILD_SRC" ]]; then
  if [[ -f "$UPLOAD_DIR/index.html" ]]; then
    BUILD_SRC="$UPLOAD_DIR"
  else
    BUILD_SRC="$(pwd)"
  fi
fi
BUILD_SRC="$(cd "$BUILD_SRC" && pwd)"

if [[ ! -f "$BUILD_SRC/index.html" ]]; then
  echo "ERROR: index.html not found in: $BUILD_SRC" >&2
  echo "Upload WebGL build to: $UPLOAD_DIR/" >&2
  exit 1
fi

echo "==> Deploy $GAME @ $VERSION"
echo "    Source: $BUILD_SRC"
echo "    Target: $VERSION_DIR"

rm -rf "$VERSION_DIR"
mkdir -p "$VERSION_DIR"
rsync -a --delete \
  --exclude '.git' \
  --exclude '.git/' \
  "$BUILD_SRC"/ "$VERSION_DIR"/

# Record metadata
cat > "$VERSION_DIR/.deploy-meta.json" <<EOF
{
  "game": "$GAME",
  "version": "$VERSION",
  "deployed_at": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
  "source": "$BUILD_SRC"
}
EOF

# Symlink web → builds/<version>
ln -sfn "builds/$VERSION" "$WEB_LINK"
echo "    web -> builds/$VERSION"

# Optional: latest pointer
ln -sfn "$VERSION" "$BUILDS_DIR/latest"
echo "    builds/latest -> $VERSION"

PUBLIC_URL="${STAGE_BASE_URL:-https://stage.sana.adlr.ir}/games/$GAME/"
echo "    URL: $PUBLIC_URL"

if [[ "$RELOAD_NGINX" == "1" ]] && docker ps --format '{{.Names}}' | grep -qx "$NGINX_CONTAINER"; then
  docker exec "$NGINX_CONTAINER" nginx -t
  docker exec "$NGINX_CONTAINER" nginx -s reload
  echo "==> nginx reloaded ($NGINX_CONTAINER)"
fi

echo "==> Done."
