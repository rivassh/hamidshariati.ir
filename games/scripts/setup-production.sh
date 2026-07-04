#!/usr/bin/env bash
# One-time production setup for Unity games platform
set -euo pipefail

GAMES_ROOT="${GAMES_ROOT:-/opt/games}"
SCRIPTS_ROOT="${SCRIPTS_ROOT:-/opt/scripts}"
NGINX_COMPOSE="${NGINX_COMPOSE:-/opt/nginx/docker-compose.yml}"

echo "==> Directories"
mkdir -p "$GAMES_ROOT" "$GAMES_ROOT/scripts" "$GAMES_ROOT/docs" "$SCRIPTS_ROOT"

echo "==> Script permissions"
chmod +x "$GAMES_ROOT"/scripts/*.sh "$SCRIPTS_ROOT"/*.sh 2>/dev/null || true

if [[ ! -d "$GAMES_ROOT/game1" ]]; then
  "$GAMES_ROOT/scripts/init-game.sh" game1
fi
if [[ ! -d "$GAMES_ROOT/game2" ]]; then
  "$GAMES_ROOT/scripts/init-game.sh" game2
fi

for g in game1 game2; do
  mkdir -p "$GAMES_ROOT/$g"/{web,android,builds,repo,upload}
  touch "$GAMES_ROOT/$g/upload/.gitkeep"
done

echo "==> nginx_proxy"
if [[ -f "$NGINX_COMPOSE" ]]; then
  cd "$(dirname "$NGINX_COMPOSE")"
  docker compose up -d nginx
  sleep 2
  docker exec nginx_proxy nginx -t
  docker exec nginx_proxy nginx -s reload
fi

echo "==> Done"
echo "WebGL URL: https://stage.sana.adlr.ir/games/<game-name>/"
echo "Deploy:    $SCRIPTS_ROOT/deploy-game.sh <game> <version>"
echo "Android:   $SCRIPTS_ROOT/deploy-android.sh <game> <version>"
