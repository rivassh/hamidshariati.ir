#!/usr/bin/env bash
# Deploy Unity Android APK for a game version.
# Usage: deploy-android.sh <game-name> <version> [apk-path]
#
# Default APK path (if omitted):
#   /opt/games/<game-name>/upload/app.apk
#
# Example:
#   deploy-android.sh space-shooter v1.0
#   deploy-android.sh space-shooter v1.0 ./release.apk
#
set -euo pipefail

GAMES_ROOT="${GAMES_ROOT:-/opt/games}"

GAME="${1:?game-name required}"
VERSION="${2:?version required (e.g. v1.0)}"
APK_SRC="${3:-}"

if [[ ! "$GAME" =~ ^[a-zA-Z0-9_-]+$ ]]; then
  echo "ERROR: game-name must match [a-zA-Z0-9_-]+" >&2
  exit 1
fi
if [[ ! "$VERSION" =~ ^[a-zA-Z0-9._-]+$ ]]; then
  echo "ERROR: version must match [a-zA-Z0-9._-]+" >&2
  exit 1
fi

GAME_DIR="$GAMES_ROOT/$GAME"
ANDROID_DIR="$GAME_DIR/android"
UPLOAD_DIR="$GAME_DIR/upload"

if [[ ! -d "$GAME_DIR" ]]; then
  echo "ERROR: game directory not found: $GAME_DIR" >&2
  exit 1
fi

if [[ -z "$APK_SRC" ]]; then
  if [[ -f "$UPLOAD_DIR/app.apk" ]]; then
    APK_SRC="$UPLOAD_DIR/app.apk"
  elif [[ -f "$UPLOAD_DIR/${GAME}.apk" ]]; then
    APK_SRC="$UPLOAD_DIR/${GAME}.apk"
  else
    APK_SRC="$(find "$UPLOAD_DIR" -maxdepth 1 -name '*.apk' -type f 2>/dev/null | head -1)"
  fi
fi

if [[ -z "$APK_SRC" || ! -f "$APK_SRC" ]]; then
  echo "ERROR: APK not found. Copy to $UPLOAD_DIR/app.apk or pass path as 3rd argument." >&2
  exit 1
fi

mkdir -p "$ANDROID_DIR" "$UPLOAD_DIR"
DEST="$ANDROID_DIR/${VERSION}.apk"

cp -f "$APK_SRC" "$DEST"
chmod 644 "$DEST"

cat > "$ANDROID_DIR/${VERSION}.meta.json" <<EOF
{
  "game": "$GAME",
  "version": "$VERSION",
  "file": "$(basename "$DEST")",
  "stored_at": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
  "source": "$APK_SRC"
}
EOF

ln -sfn "${VERSION}.apk" "$ANDROID_DIR/latest.apk"

echo "==> Android deploy $GAME @ $VERSION"
echo "    APK: $DEST"
echo "    latest -> ${VERSION}.apk"
