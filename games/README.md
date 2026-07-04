# Unity Games Hosting (`/opt/games`)

Multi-game Unity WebGL + Android artifact hosting for **nginx_proxy** + **stage-deployer**.

## Quick start

```bash
/opt/games/scripts/init-game.sh game1
/opt/games/scripts/deploy-game.sh game1 v1.0 /path/to/WebGL/build
```

Open: **https://stage.sana.adlr.ir/games/game1/**

## Scripts

| Script | Purpose |
|--------|---------|
| `scripts/init-game.sh` | Create game folder structure |
| `scripts/deploy-game.sh` | Deploy versioned WebGL build |
| `scripts/build-android.sh` | Store versioned APK |
| `scripts/regenerate-games-nginx.sh` | List games + reload nginx |

## Docs

- [GIT-WORKFLOW.md](docs/GIT-WORKFLOW.md)
