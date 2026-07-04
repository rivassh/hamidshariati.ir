#!/usr/bin/env bash
# Create a new game slot under /opt/games
# Usage: init-game.sh <game-name>
#
set -euo pipefail

GAMES_ROOT="${GAMES_ROOT:-/opt/games}"
GAME="${1:?game-name required}"

if [[ ! "$GAME" =~ ^[a-zA-Z0-9_-]+$ ]]; then
  echo "ERROR: game-name must match [a-zA-Z0-9_-]+" >&2
  exit 1
fi

GAME_DIR="$GAMES_ROOT/$GAME"
if [[ -e "$GAME_DIR" ]]; then
  echo "ERROR: already exists: $GAME_DIR" >&2
  exit 1
fi

mkdir -p "$GAME_DIR"/{builds,android,repo,upload}
mkdir -p "$GAME_DIR/builds/v0.0.0-placeholder"/{Build,TemplateData}

cat > "$GAME_DIR/builds/v0.0.0-placeholder/index.html" <<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <base href="/games/GAME_SLUG/" />
  <title>Unity WebGL — GAME_SLUG</title>
  <link rel="stylesheet" href="TemplateData/style.css" />
</head>
<body>
  <p>Deploy a Unity WebGL build with:</p>
  <pre>deploy-game.sh GAME_SLUG v1.0 /path/to/build</pre>
</body>
</html>
HTML
sed -i "s/GAME_SLUG/$GAME/g" "$GAME_DIR/builds/v0.0.0-placeholder/index.html"

cat > "$GAME_DIR/builds/v0.0.0-placeholder/TemplateData/style.css" <<'CSS'
body { font-family: system-ui, sans-serif; background: #0f172a; color: #e2e8f0; padding: 2rem; }
CSS

ln -sfn "builds/v0.0.0-placeholder" "$GAME_DIR/web"

# Git repo (deployment / manifests — not Unity source unless you add it)
if [[ ! -d "$GAME_DIR/repo/.git" ]]; then
  git -C "$GAME_DIR/repo" init -b main
fi

cat > "$GAME_DIR/repo/.gitignore" <<'GIT'
# WebGL build artifacts committed only if you use CI → build-output/
build-output/tmp/
*.log
.DS_Store
GIT

cat > "$GAME_DIR/repo/README.md" <<EOF
# $GAME

Unity game deployment repo.

## WebGL URL

\`https://stage.sana.adlr.ir/games/$GAME/\`

## Deploy WebGL

\`\`\`bash
# rsync WebGL build to upload/ then:
/opt/scripts/deploy-game.sh $GAME v1.0
\`\`\`

## Deploy Android APK

\`\`\`bash
cp app.apk upload/app.apk
/opt/scripts/deploy-android.sh $GAME v1.0
\`\`\`

## Git workflow

\`\`\`bash
git add .
git commit -m "release: v1.0"
git tag v1.0
git push origin main --tags
\`\`\`

See \`/opt/games/docs/GIT-WORKFLOW.md\`.
EOF

cp "$GAMES_ROOT/scripts/hooks/post-receive.sample" "$GAME_DIR/repo/hooks/post-receive.sample" 2>/dev/null || true

cat > "$GAME_DIR/upload/README.txt" <<EOF
Upload Unity build artifacts here before deploy:

WebGL: copy index.html, Build/, TemplateData/ into this folder
       then: /opt/scripts/deploy-game.sh $GAME v1.0

Android: copy app.apk here
         then: /opt/scripts/deploy-android.sh $GAME v1.0
EOF

touch "$GAME_DIR/builds/.gitkeep" "$GAME_DIR/android/.gitkeep" "$GAME_DIR/upload/.gitkeep"

echo "Created: $GAME_DIR"
echo "  web  -> builds/v0.0.0-placeholder"
echo "  URL:  https://stage.sana.adlr.ir/games/$GAME/"
echo "Next:   deploy-game.sh $GAME v1.0 <unity-webgl-output>"
