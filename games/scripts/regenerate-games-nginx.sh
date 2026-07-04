#!/usr/bin/env bash
# Optional: list registered games and reload nginx (config is dynamic via games-webgl.inc)
set -euo pipefail

GAMES_ROOT="${GAMES_ROOT:-/opt/games}"
NGINX_CONTAINER="${NGINX_CONTAINER_NAME:-nginx_proxy}"

echo "# Games registered under $GAMES_ROOT"
for dir in "$GAMES_ROOT"/*; do
  [[ -d "$dir" ]] || continue
  name="$(basename "$dir")"
  [[ "$name" == "scripts" || "$name" == "docs" ]] && continue
  [[ -L "$dir/web" || -d "$dir/web" ]] || continue
  target=""
  if [[ -L "$dir/web" ]]; then
    target="$(readlink "$dir/web")"
  fi
  echo "  - $name  web->$target  URL: /games/$name/"
done

if docker ps --format '{{.Names}}' | grep -qx "$NGINX_CONTAINER"; then
  docker exec "$NGINX_CONTAINER" nginx -t
  docker exec "$NGINX_CONTAINER" nginx -s reload
  echo "nginx reloaded."
fi
