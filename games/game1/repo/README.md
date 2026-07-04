# game1

Unity game deployment repo.

## WebGL URL

`https://stage.sana.adlr.ir/games/game1/`

## Deploy WebGL

```bash
/opt/games/scripts/deploy-game.sh game1 v1.0 /path/to/WebGL/build
```

## Deploy Android APK

```bash
/opt/games/scripts/build-android.sh game1 v1.0 /path/to/app.apk
```

## Git workflow

```bash
git add .
git commit -m "release: v1.0"
git tag v1.0
git push origin main --tags
```

See `/opt/games/docs/GIT-WORKFLOW.md`.
