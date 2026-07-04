# Unity Games — Git & Deploy Workflow

Games root on server: **`/opt/games`** (mounted in nginx as **`/games`**).

Public URL pattern: **`https://stage.sana.adlr.ir/games/<game-name>/`**

---

## Folder layout (per game)

```
/opt/games/<game-name>/
├── web/              → symlink to builds/<version> (live site)
├── builds/
│   ├── v1.0/
│   ├── v1.1/
│   └── latest/       → symlink to active version (optional pointer)
├── android/
│   ├── v1.0.apk
│   └── latest.apk    → symlink
└── repo/             → git repository (manifests, CI, hooks)
```

---

## 1. Create a new game

```bash
/opt/games/scripts/init-game.sh space-shooter
```

---

## 2. Unity WebGL build (local / CI)

1. In Unity: **File → Build Settings → WebGL → Build**.
2. Output folder contains `index.html`, `Build/`, `TemplateData/`.

Ensure `index.html` uses:

```html
<base href="/games/<game-name>/" />
```

---

## 3. Deploy WebGL (manual)

```bash
/opt/games/scripts/deploy-game.sh <game-name> <version> <path-to-build>

# Example
/opt/games/scripts/deploy-game.sh space-shooter v1.0 ./WebGL-Build
```

What it does:

1. Copies build → `/opt/games/<game>/builds/<version>/`
2. Updates symlink `/opt/games/<game>/web` → `builds/<version>`
3. Sets `builds/latest` → `<version>`
4. Reloads **nginx_proxy** (if running)

---

## 4. Android APK

```bash
/opt/games/scripts/build-android.sh <game-name> <version> <path-to.apk>

# Example
/opt/games/scripts/build-android.sh space-shooter v1.0 ./app-release.apk
```

APK path: `/opt/games/<game>/android/<version>.apk`

---

## 5. Git workflow (per game repo)

```bash
cd /opt/games/<game-name>/repo

# Track deployment notes, CI config, optional build-output/
git add .
git commit -m "chore: prepare v1.0 deploy"
git tag v1.0
```

### Push from CI / laptop

```bash
git remote add origin git@gitlab.example.com:games/<game-name>.git
git push -u origin main --tags
```

### Auto-deploy on tag (optional)

1. Copy WebGL build into `repo/build-output/` before push, **or** CI uploads artifacts to server.
2. Install hook on server:

```bash
cp /opt/games/scripts/hooks/post-receive.sample \
   /opt/games/<game-name>/repo/.git/hooks/post-receive
chmod +x /opt/games/<game-name>/repo/.git/hooks/post-receive
```

3. Push tag `v1.0` → hook runs `deploy-game.sh`.

For GitLab CI, call `deploy-game.sh` over SSH after build instead of bare-repo hooks.

---

## 6. Versioning rules

| Path | Purpose |
|------|---------|
| `builds/v1.0/` | Immutable WebGL snapshot |
| `builds/v2.0/` | Next release |
| `web` | Symlink → active version (nginx serves this) |
| `android/v1.0.apk` | Android release artifact |

Rollback:

```bash
ln -sfn builds/v1.0 /opt/games/<game>/web
docker exec nginx_proxy nginx -s reload
```

---

## 7. Nginx / Docker

- Config: `/opt/stage-deployer/nginx/games-webgl.inc`
- Mount: `/opt/games:/games:ro` on **nginx_proxy**
- MIME: `.wasm`, `.data`, `.js`, `.symbols.json` + gzip + large files

Reload:

```bash
/opt/games/scripts/regenerate-games-nginx.sh
```

---

## 8. stage-deployer

Phaser/HTML games can stay on `/opt/<slug>/` via stage-deployer.

Unity games use **`/opt/games/<name>/`** and URL **`/games/<name>/`** — separate from per-slug stage paths.
