# Unity Games Platform — Production Commands

Server root: `/opt/games` (nginx: `/games`)  
Scripts: `/opt/scripts/`  
Domain: `https://stage.sana.adlr.ir/games/<game-name>/`

Unity runs **locally only**. Server receives builds only.

---

## One-time setup

```bash
/opt/games/scripts/setup-production.sh
```

## New game

```bash
/opt/games/scripts/init-game.sh my-game
```

## Folder layout (per game)

```
/opt/games/my-game/
├── upload/      ← put WebGL build or app.apk here
├── builds/v1.0/
├── web → builds/v1.0
├── android/v1.0.apk
└── repo/        ← git
```

## WebGL deploy

```bash
# 1. Copy Unity WebGL output to upload/
rsync -av ./WebGL-Build/ /opt/games/my-game/upload/

# 2. Deploy
/opt/scripts/deploy-game.sh my-game v1.0
```

## Android deploy

```bash
cp app-release.apk /opt/games/my-game/upload/app.apk
/opt/scripts/deploy-android.sh my-game v1.0
```

## Git (per game)

```bash
cd /opt/games/my-game/repo
git add .
git commit -m "release: v1.0"
git tag v1.0
git push origin main --tags
/opt/scripts/deploy-game.sh my-game v1.0
```

## Rollback WebGL

```bash
ln -sfn builds/v1.0 /opt/games/my-game/web
docker exec nginx_proxy nginx -s reload
```

## Reload nginx

```bash
/opt/games/scripts/regenerate-games-nginx.sh
```

## Unity WebGL index.html

```html
<base href="/games/my-game/" />
```
