# game2 — Git tagging

```bash
cd /opt/games/game2/repo
git add .
git commit -m "release: v1.0"
git tag v1.0
```

Upload WebGL to `../upload/` then:

```bash
/opt/scripts/deploy-game.sh game2 v1.0
```
