/**
 * بازی مار — پلتفرم وب (Phaser 3)
 * بدون build — index.html + game.js + style.css
 */

(function () {
  "use strict";

  const GRID_COLS = 20;
  const GRID_ROWS = 20;
  const CELL = 20;
  const BOARD_W = GRID_COLS * CELL;
  const BOARD_H = GRID_ROWS * CELL;
  const HUD_H = 56;
  const GAME_H = BOARD_H + HUD_H;

  const BASE_TICK = 130;
  const TICK_STEP = 14;
  const MIN_TICK = 52;
  const HIGH_SCORE_KEY = "phaser_snake_high_score";

  const FOOD_COLORS = [
    { fill: 0xf472b6, glow: 0xec4899 },
    { fill: 0x22d3ee, glow: 0x06b6d4 },
    { fill: 0xfacc15, glow: 0xeab308 },
    { fill: 0xa78bfa, glow: 0x8b5cf6 },
    { fill: 0x4ade80, glow: 0x22c55e },
    { fill: 0xfb923c, glow: 0xf97316 },
  ];

  const DIR = {
    up: { x: 0, y: -1, key: "up" },
    down: { x: 0, y: 1, key: "down" },
    left: { x: -1, y: 0, key: "left" },
    right: { x: 1, y: 0, key: "right" },
  };

  let sharedHighScore = 0;

  function loadHighScore() {
    const v = parseInt(localStorage.getItem(HIGH_SCORE_KEY) || "0", 10);
    sharedHighScore = Number.isFinite(v) ? v : 0;
  }

  function saveHighScore(score) {
    if (score > sharedHighScore) {
      sharedHighScore = score;
      localStorage.setItem(HIGH_SCORE_KEY, String(sharedHighScore));
      return true;
    }
    return false;
  }

  function tickMs(score) {
    const tier = Math.floor(score / 5);
    return Math.max(MIN_TICK, BASE_TICK - tier * TICK_STEP);
  }

  function speedLevel(score) {
    return Math.floor(score / 5) + 1;
  }

  function opposite(a, b) {
    return (
      (a === DIR.up && b === DIR.down) ||
      (a === DIR.down && b === DIR.up) ||
      (a === DIR.left && b === DIR.right) ||
      (a === DIR.right && b === DIR.left)
    );
  }

  function getAudioContext(scene) {
    const sound = scene.game && scene.game.sound;
    return sound && sound.context ? sound.context : null;
  }

  function unlockAudio(scene) {
    const ctx = getAudioContext(scene);
    if (ctx && ctx.state === "suspended") {
      ctx.resume();
    }
  }

  function playEatSound(scene) {
    const ctx = getAudioContext(scene);
    if (!ctx) return;
    try {
      const t = ctx.currentTime;
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = "square";
      osc.frequency.setValueAtTime(520, t);
      osc.frequency.exponentialRampToValueAtTime(880, t + 0.08);
      gain.gain.setValueAtTime(0.12, t);
      gain.gain.exponentialRampToValueAtTime(0.01, t + 0.12);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start(t);
      osc.stop(t + 0.12);
    } catch (_) {
      /* ignore audio errors */
    }
  }

  function playGameOverSound(scene) {
    const ctx = getAudioContext(scene);
    if (!ctx) return;
    try {
      const t = ctx.currentTime;
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = "sawtooth";
      osc.frequency.setValueAtTime(200, t);
      osc.frequency.exponentialRampToValueAtTime(70, t + 0.35);
      gain.gain.setValueAtTime(0.1, t);
      gain.gain.exponentialRampToValueAtTime(0.01, t + 0.4);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start(t);
      osc.stop(t + 0.4);
    } catch (_) {
      /* ignore */
    }
  }

  class BootScene extends Phaser.Scene {
    constructor() {
      super({ key: "BootScene" });
    }

    preload() {
      /* بدون asset خارجی — همه چیز با گرافیک Phaser */
    }

    create() {
      loadHighScore();
      this.scene.start("MenuScene");
    }
  }

  class MenuScene extends Phaser.Scene {
    constructor() {
      super({ key: "MenuScene" });
    }

    create() {
      this.drawGridBackground(0x0a0614, 0x12101f, 0x0c0a18);

      const title = this.add
        .text(BOARD_W / 2, GAME_H / 2 - 70, "بازی مار", {
          fontFamily: "system-ui, sans-serif",
          fontSize: "36px",
          color: "#a5f3fc",
          fontStyle: "bold",
        })
        .setOrigin(0.5);
      title.setShadow(0, 0, "#22d3ee", 12, true, true);

      this.add
        .text(BOARD_W / 2, GAME_H / 2 - 20, "بهترین رکورد: " + sharedHighScore, {
          fontFamily: "system-ui, sans-serif",
          fontSize: "18px",
          color: "#f472b6",
        })
        .setOrigin(0.5);

      const btn = this.createButton(
        BOARD_W / 2,
        GAME_H / 2 + 40,
        "شروع بازی",
        () => {
          this.scene.start("PlayScene");
        }
      );

      this.add
        .text(BOARD_W / 2, GAME_H - 36, "فلش‌ها · کلیک روی صفحه برای جهت", {
          fontFamily: "system-ui, sans-serif",
          fontSize: "14px",
          color: "#94a3b8",
        })
        .setOrigin(0.5);

      this.input.keyboard.once("keydown-SPACE", () => btn.emit("pointerdown"));
      this.input.keyboard.once("keydown-ENTER", () => btn.emit("pointerdown"));
      this.input.once("pointerdown", () => unlockAudio(this));
    }

    drawGridBackground(dark, altA, altB) {
      const g = this.add.graphics();
      for (let y = 0; y < GRID_ROWS; y++) {
        for (let x = 0; x < GRID_COLS; x++) {
          g.fillStyle((x + y) % 2 === 0 ? altA : altB, 1);
          g.fillRect(x * CELL, y * CELL + HUD_H, CELL, CELL);
        }
      }
      g.lineStyle(1, 0x22d3ee, 0.15);
      for (let x = 0; x <= GRID_COLS; x++) {
        g.lineBetween(x * CELL, HUD_H, x * CELL, GAME_H);
      }
      for (let y = 0; y <= GRID_ROWS; y++) {
        g.lineBetween(0, HUD_H + y * CELL, BOARD_W, HUD_H + y * CELL);
      }
    }

    createButton(x, y, label, onClick) {
      const container = this.add.container(x, y);
      const bg = this.add
        .rectangle(0, 0, 200, 48, 0x22d3ee, 0.25)
        .setStrokeStyle(2, 0xa855f7, 0.9);
      const text = this.add
        .text(0, 0, label, {
          fontFamily: "system-ui, sans-serif",
          fontSize: "20px",
          color: "#e2e8f0",
          fontStyle: "bold",
        })
        .setOrigin(0.5);
      container.add([bg, text]);
      bg.setInteractive({ useHandCursor: true });
      bg.on("pointerover", () => bg.setFillStyle(0xa855f7, 0.35));
      bg.on("pointerout", () => bg.setFillStyle(0x22d3ee, 0.25));
      bg.on("pointerdown", onClick);
      return bg;
    }
  }

  class PlayScene extends Phaser.Scene {
    constructor() {
      super({ key: "PlayScene" });
    }

    create() {
      this.boardOriginY = HUD_H;
      this.snake = [];
      this.direction = DIR.right;
      this.nextDirection = DIR.right;
      this.food = null;
      this.score = 0;
      this.particles = [];
      this.foodPulse = 0;
      this.gameOverReason = "";
      this.isRunning = true;

      this.gridGfx = this.add.graphics();
      this.snakeGfx = this.add.graphics();
      this.foodGfx = this.add.graphics();
      this.fxGfx = this.add.graphics();

      this.scoreText = this.add.text(16, 14, "امتیاز: 0", {
        fontFamily: "system-ui, sans-serif",
        fontSize: "20px",
        color: "#fef08a",
        fontStyle: "bold",
      });
      this.highText = this.add
        .text(BOARD_W - 16, 14, "رکورد: " + sharedHighScore, {
          fontFamily: "system-ui, sans-serif",
          fontSize: "16px",
          color: "#f472b6",
        })
        .setOrigin(1, 0);
      this.speedText = this.add
        .text(BOARD_W / 2, 14, "سرعت: 1", {
          fontFamily: "system-ui, sans-serif",
          fontSize: "16px",
          color: "#94a3b8",
        })
        .setOrigin(0.5, 0);

      this.hudLine = this.add.graphics();
      this.hudLine.lineStyle(2, 0x22d3ee, 0.4);
      this.hudLine.lineBetween(0, HUD_H - 1, BOARD_W, HUD_H - 1);

      this.setupInput();
      unlockAudio(this);
      this.resetRound();
      this.scheduleTick();
    }

    setupInput() {
      this.cursors = this.input.keyboard.createCursorKeys();

      this.input.on("pointerdown", (pointer) => {
        if (!this.isRunning || !this.snake.length) return;
        const head = this.snake[0];
        const hx = head.x * CELL + CELL / 2;
        const hy = this.boardOriginY + head.y * CELL + CELL / 2;
        const dx = pointer.x - hx;
        const dy = pointer.y - hy;
        if (Math.abs(dx) < 8 && Math.abs(dy) < 8) return;
        let dir;
        if (Math.abs(dx) > Math.abs(dy)) {
          dir = dx > 0 ? DIR.right : DIR.left;
        } else {
          dir = dy > 0 ? DIR.down : DIR.up;
        }
        if (!opposite(this.direction, dir)) {
          this.nextDirection = dir;
        }
      });
    }

    resetRound() {
      const startX = Math.floor(GRID_COLS / 2);
      const startY = Math.floor(GRID_ROWS / 2);
      this.snake = [
        { x: startX, y: startY },
        { x: startX - 1, y: startY },
        { x: startX - 2, y: startY },
      ];
      this.direction = DIR.right;
      this.nextDirection = DIR.right;
      this.score = 0;
      this.particles = [];
      this.isRunning = true;
      this.updateHud();
      this.spawnFood();
      this.redraw();
    }

    scheduleTick() {
      if (this.tickEvent) this.tickEvent.remove(false);
      this.tickEvent = this.time.addEvent({
        delay: tickMs(this.score),
        callback: this.step,
        callbackScope: this,
        loop: true,
      });
    }

    refreshTickSpeed() {
      if (!this.isRunning) return;
      this.scheduleTick();
    }

    spawnFood() {
      let spot;
      let tries = 0;
      do {
        spot = {
          x: Phaser.Math.Between(0, GRID_COLS - 1),
          y: Phaser.Math.Between(0, GRID_ROWS - 1),
        };
        tries++;
      } while (this.snakeOccupies(spot) && tries < 500);

      const palette = Phaser.Utils.Array.GetRandom(FOOD_COLORS);
      this.food = { ...spot, ...palette };
      this.foodPulse = 0;
    }

    snakeOccupies(cell) {
      return this.snake.some((p) => p.x === cell.x && p.y === cell.y);
    }

    step() {
      if (!this.isRunning) return;

      if (this.cursors.left.isDown) this.tryDir(DIR.left);
      if (this.cursors.right.isDown) this.tryDir(DIR.right);
      if (this.cursors.up.isDown) this.tryDir(DIR.up);
      if (this.cursors.down.isDown) this.tryDir(DIR.down);

      this.direction = this.nextDirection;
      const head = this.snake[0];
      const newHead = {
        x: head.x + this.direction.x,
        y: head.y + this.direction.y,
      };

      if (
        newHead.x < 0 ||
        newHead.y < 0 ||
        newHead.x >= GRID_COLS ||
        newHead.y >= GRID_ROWS
      ) {
        this.endGame("به دیوار خوردی!");
        return;
      }

      const ate = this.food && newHead.x === this.food.x && newHead.y === this.food.y;
      const bodyCheck = ate ? this.snake : this.snake.slice(0, -1);
      if (bodyCheck.some((p) => p.x === newHead.x && p.y === newHead.y)) {
        this.endGame("به خودت خوردی!");
        return;
      }

      this.snake.unshift(newHead);

      if (ate) {
        const prevTier = Math.floor((this.score - 0) / 5);
        this.score++;
        const newTier = Math.floor(this.score / 5);
        this.spawnEatFx(this.food.x, this.food.y, this.food.fill, this.food.glow);
        playEatSound(this);
        this.spawnFood();
        this.updateHud();
        if (newTier > prevTier) this.refreshTickSpeed();
      } else {
        this.snake.pop();
      }

      this.foodPulse += 0.2;
      this.updateParticles();
      this.redraw();
    }

    tryDir(dir) {
      if (!opposite(this.direction, dir)) {
        this.nextDirection = dir;
      }
    }

    spawnEatFx(gx, gy, color, glow) {
      const cx = gx * CELL + CELL / 2;
      const cy = this.boardOriginY + gy * CELL + CELL / 2;
      for (let i = 0; i < 12; i++) {
        const angle = (Math.PI * 2 * i) / 12;
        const speed = Phaser.Math.FloatBetween(1.5, 4);
        this.particles.push({
          x: cx,
          y: cy,
          vx: Math.cos(angle) * speed,
          vy: Math.sin(angle) * speed,
          life: 1,
          color,
          glow,
          ring: false,
        });
      }
      this.particles.push({
        x: cx,
        y: cy,
        vx: 0,
        vy: 0,
        life: 1,
        color,
        glow,
        ring: true,
        ringR: 6,
      });
    }

    updateParticles() {
      this.particles = this.particles.filter((p) => p.life > 0.03);
      for (const p of this.particles) {
        p.life *= 0.86;
        if (p.ring) {
          p.ringR += 2;
        } else {
          p.x += p.vx;
          p.y += p.vy;
          p.vx *= 0.9;
          p.vy *= 0.9;
        }
      }
    }

    updateHud() {
      this.scoreText.setText("امتیاز: " + this.score);
      this.highText.setText("رکورد: " + sharedHighScore);
      this.speedText.setText("سرعت: " + speedLevel(this.score));
    }

    endGame(reason) {
      this.isRunning = false;
      this.gameOverReason = reason;
      if (this.tickEvent) this.tickEvent.remove(false);
      playGameOverSound(this);
      const isNew = saveHighScore(this.score);
      this.scene.start("GameOverScene", {
        score: this.score,
        highScore: sharedHighScore,
        reason: reason,
        isNewRecord: isNew,
      });
    }

    redraw() {
      this.drawGrid();
      this.drawFood();
      this.drawSnake();
      this.drawParticles();
    }

    drawGrid() {
      const g = this.gridGfx;
      g.clear();
      for (let y = 0; y < GRID_ROWS; y++) {
        for (let x = 0; x < GRID_COLS; x++) {
          const alt = (x + y) % 2 === 0;
          g.fillStyle(alt ? 0x12101f : 0x0c0a18, 1);
          g.fillRect(x * CELL, this.boardOriginY + y * CELL, CELL, CELL);
        }
      }
      g.lineStyle(1, 0x22d3ee, 0.12);
      for (let x = 0; x <= GRID_COLS; x++) {
        g.lineBetween(x * CELL, this.boardOriginY, x * CELL, GAME_H);
      }
      for (let y = 0; y <= GRID_ROWS; y++) {
        g.lineBetween(0, this.boardOriginY + y * CELL, BOARD_W, this.boardOriginY + y * CELL);
      }
      g.lineStyle(2, 0xa855f7, 0.35);
      g.strokeRect(1, this.boardOriginY + 1, BOARD_W - 2, BOARD_H - 2);
    }

    drawFood() {
      const g = this.foodGfx;
      g.clear();
      if (!this.food) return;
      const pulse = 0.85 + Math.sin(this.foodPulse) * 0.12;
      const r = (CELL * pulse) / 2 - 2;
      const cx = this.food.x * CELL + CELL / 2;
      const cy = this.boardOriginY + this.food.y * CELL + CELL / 2;
      g.fillStyle(this.food.fill, 1);
      g.fillCircle(cx, cy, r);
      g.fillStyle(0xffffff, 0.45);
      g.fillCircle(cx - 3, cy - 3, 3);
    }

    drawSnake() {
      const g = this.snakeGfx;
      g.clear();
      this.snake.forEach((part, i) => {
        const px = part.x * CELL + (i === 0 ? 1 : 2);
        const py = this.boardOriginY + part.y * CELL + (i === 0 ? 1 : 2);
        const size = CELL - (i === 0 ? 2 : 4);
        const head = i === 0;
        g.fillStyle(head ? 0xa5f3fc : Phaser.Display.Color.GetColor(34 + i * 2, 200 - i * 3, 238), 1);
        g.fillRoundedRect(px, py, size, size, head ? 6 : 4);
        if (head) {
          g.fillStyle(0x0f172a, 1);
          const cx = part.x * CELL + CELL / 2;
          const cy = this.boardOriginY + part.y * CELL + CELL / 2;
          const ex = this.direction.x * 3;
          const ey = this.direction.y * 3;
          g.fillCircle(cx - 4 + ex, cy - 3 + ey, 2.5);
          g.fillCircle(cx + 4 + ex, cy - 3 + ey, 2.5);
        }
      });
    }

    drawParticles() {
      const g = this.fxGfx;
      g.clear();
      for (const p of this.particles) {
        const alpha = p.life;
        if (p.ring) {
          g.lineStyle(3, p.glow, alpha);
          g.strokeCircle(p.x, p.y, p.ringR);
        } else {
          g.fillStyle(p.color, alpha);
          g.fillCircle(p.x, p.y, 4 * alpha + 1);
        }
      }
    }
  }

  class GameOverScene extends Phaser.Scene {
    constructor() {
      super({ key: "GameOverScene" });
    }

    init(data) {
      this.finalScore = data.score || 0;
      this.finalHigh = data.highScore || 0;
      this.reason = data.reason || "بازی تمام شد";
      this.isNewRecord = !!data.isNewRecord;
    }

    create() {
      this.cameras.main.setBackgroundColor(0x0a0a12);

      const overlay = this.add.rectangle(
        BOARD_W / 2,
        GAME_H / 2,
        BOARD_W,
        GAME_H,
        0x0f0a1e,
        0.88
      );

      this.add
        .text(BOARD_W / 2, GAME_H / 2 - 100, "Game Over", {
          fontFamily: "system-ui, sans-serif",
          fontSize: "42px",
          color: "#f87171",
          fontStyle: "bold",
        })
        .setOrigin(0.5);

      this.add
        .text(BOARD_W / 2, GAME_H / 2 - 48, this.reason, {
          fontFamily: "system-ui, sans-serif",
          fontSize: "18px",
          color: "#cbd5e1",
        })
        .setOrigin(0.5);

      this.add
        .text(BOARD_W / 2, GAME_H / 2, "امتیاز: " + this.finalScore, {
          fontFamily: "system-ui, sans-serif",
          fontSize: "22px",
          color: "#fef08a",
        })
        .setOrigin(0.5);

      this.add
        .text(BOARD_W / 2, GAME_H / 2 + 36, "بهترین رکورد: " + this.finalHigh, {
          fontFamily: "system-ui, sans-serif",
          fontSize: "18px",
          color: "#f472b6",
        })
        .setOrigin(0.5);

      if (this.isNewRecord) {
        this.add
          .text(BOARD_W / 2, GAME_H / 2 + 68, "رکورد جدید!", {
            fontFamily: "system-ui, sans-serif",
            fontSize: "18px",
            color: "#4ade80",
            fontStyle: "bold",
          })
          .setOrigin(0.5);
      }

      const restartBtn = this.add
        .rectangle(BOARD_W / 2, GAME_H / 2 + 120, 220, 50, 0x22d3ee, 0.3)
        .setStrokeStyle(2, 0xa855f7, 1)
        .setInteractive({ useHandCursor: true });

      const restartLabel = this.add
        .text(BOARD_W / 2, GAME_H / 2 + 120, "Restart", {
          fontFamily: "system-ui, sans-serif",
          fontSize: "20px",
          color: "#f8fafc",
          fontStyle: "bold",
        })
        .setOrigin(0.5);

      restartBtn.on("pointerover", () => restartBtn.setFillStyle(0xa855f7, 0.4));
      restartBtn.on("pointerout", () => restartBtn.setFillStyle(0x22d3ee, 0.3));
      restartBtn.on("pointerdown", () => {
        unlockAudio(this);
        this.scene.start("PlayScene");
      });

      this.add
        .text(BOARD_W / 2, GAME_H - 28, "منو: M", {
          fontFamily: "system-ui, sans-serif",
          fontSize: "13px",
          color: "#64748b",
        })
        .setOrigin(0.5);

      this.input.keyboard.on("keydown-R", () => this.scene.start("PlayScene"));
      this.input.keyboard.on("keydown-ENTER", () => this.scene.start("PlayScene"));
      this.input.keyboard.on("keydown-M", () => this.scene.start("MenuScene"));

      overlay.setDepth(-1);
      restartBtn.setDepth(1);
      restartLabel.setDepth(2);
    }
  }

  const gameConfig = {
    type: Phaser.AUTO,
    width: BOARD_W,
    height: GAME_H,
    parent: "game-container",
    backgroundColor: "#050510",
    scene: [BootScene, MenuScene, PlayScene, GameOverScene],
    audio: {
      disableWebAudio: false,
    },
    scale: {
      mode: Phaser.Scale.NONE,
    },
  };

  loadHighScore();
  new Phaser.Game(gameConfig);
})();
