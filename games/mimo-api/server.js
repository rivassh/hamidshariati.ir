require('dotenv').config();
const http = require('http');
const { execSync } = require('child_process');
const { randomBytes } = require('crypto');
const fs = require('fs');
const { IncomingForm } = require('formidable');

const PORT = 3456;
const TMUX_SESSION = process.env.TMUX_SESSION || '2';
const TMUX_WINDOW = process.env.TMUX_WINDOW || '0';
const BOT_NAME = process.env.BOT_NAME || 'دانا';
const UPLOAD_DIR = '/tmp/chatbot-uploads';

if (!fs.existsSync(UPLOAD_DIR)) fs.mkdirSync(UPLOAD_DIR, { recursive: true });

// Job queue
const jobs = new Map();

function createJob(data) {
  const id = randomBytes(8).toString('hex');
  jobs.set(id, {
    id,
    message: data.message,
    games: data.games || ['snake'],
    status: 'queued',
    response: null,
    created: Date.now(),
    beforeLines: 0
  });
  return id;
}

function processJob(job) {
  job.status = 'processing';
  
  try {
    const sessionCheck = execSync(
      `tmux has-session -t ${TMUX_SESSION} 2>&1 && echo 'active' || echo 'inactive'`,
      { encoding: 'utf8', timeout: 5000 }
    ).trim();

    if (sessionCheck !== 'active') {
      job.status = 'error';
      job.response = `${BOT_NAME} در دسترس نیست.`;
      return;
    }

    const gameNames = { 'snake': 'مار نئون', 'game1': 'بازی ۱', 'game2': 'بازی کلمات' };
    const gamePaths = { 'snake': '/game-platform/', 'game1': '/games/game1/', 'game2': '/games/game2/' };
    const selectedGames = job.games.map(g => `${gameNames[g] || g} (${gamePaths[g] || ''})`);
    const gameList = selectedGames.join('، ');

    const prompt = `[CHATBOT]\n${gameList}\n\n${job.message}\n\nفقط فارسی ساده جواب بده.`;

    job.beforeLines = parseInt(
      execSync(`tmux capture-pane -t ${TMUX_SESSION}:${TMUX_WINDOW} -p | wc -l`, { encoding: 'utf8' }).trim()
    );

    const tmpFile = `/tmp/job-${job.id}.txt`;
    fs.writeFileSync(tmpFile, prompt, 'utf8');
    execSync(`tmux load-buffer ${tmpFile} && tmux paste-buffer -t ${TMUX_SESSION}:${TMUX_WINDOW} -d && tmux send-keys -t ${TMUX_SESSION}:${TMUX_WINDOW} Enter`);
    try { fs.unlinkSync(tmpFile); } catch (e) {}

    job.status = 'waiting';
    pollJob(job);

  } catch (e) {
    job.status = 'error';
    job.response = `خطا: ${e.message}`;
  }
}

function pollJob(job, attempt = 0) {
  if (attempt >= 60) {
    job.status = 'timeout';
    job.response = 'پاسخی دریافت نشد. دوباره امتحان کن.';
    return;
  }

  setTimeout(() => {
    try {
      const content = execSync(`tmux capture-pane -t ${TMUX_SESSION}:${TMUX_WINDOW} -p`, { encoding: 'utf8' }).trim();
      const lines = content.split('\n');
      const newLines = lines.slice(job.beforeLines);
      let response = newLines.join('\n').trim();
      
      response = response.replace(/\x1b\[[0-9;]*m/g, '');
      response = response.replace(/[\x00-\x08\x0B\x0C\x0E-\x1F]/g, '');
      response = response.trim();

      if (response && response.length > 5 && !response.includes('[CHATBOT') && response !== job.lastContent) {
        job.lastContent = response;
        job.stableCount = (job.stableCount || 0) + 1;
        
        if (job.stableCount >= 2) {
          job.status = 'done';
          job.response = response;
          return;
        }
      } else if (response) {
        job.lastContent = response;
        job.stableCount = 0;
      }

      pollJob(job, attempt + 1);
    } catch (e) {
      pollJob(job, attempt + 1);
    }
  }, 1000);
}

const server = http.createServer(async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  if (req.method === 'OPTIONS') { res.writeHead(200); res.end(); return; }

  // Health check
  if (req.method === 'GET' && req.url === '/health') {
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ status: 'ok' }));
    return;
  }

  // Config
  if (req.method === 'GET' && req.url === '/config') {
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ botName: BOT_NAME }));
    return;
  }

  // Submit job (returns immediately)
  if (req.method === 'POST' && req.url === '/chat') {
    let body = '';
    req.on('data', chunk => { body += chunk; });
    req.on('end', () => {
      try {
        const data = JSON.parse(body);
        if (!data.message) {
          res.writeHead(400, { 'Content-Type': 'application/json' });
          res.end(JSON.stringify({ error: 'Message required' }));
          return;
        }
        const jobId = createJob(data);
        processJob(jobs.get(jobId));
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ jobId, status: 'queued' }));
      } catch (e) {
        res.writeHead(400, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ error: 'Invalid JSON' }));
      }
    });
    return;
  }

  // Check job status
  const statusMatch = req.url.match(/^\/status\/([a-f0-9]+)$/);
  if (req.method === 'GET' && statusMatch) {
    const job = jobs.get(statusMatch[1]);
    if (!job) {
      res.writeHead(404, { 'Content-Type': 'application/json' });
      res.end(JSON.stringify({ error: 'Job not found' }));
      return;
    }
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({
      id: job.id,
      status: job.status,
      response: job.response
    }));
    return;
  }

  // Voice transcription
  if (req.method === 'POST' && req.url === '/transcribe') {
    const form = new IncomingForm({ uploadDir: UPLOAD_DIR, keepExtensions: true, maxFileSize: 10 * 1024 * 1024 });
    form.parse(req, async (err, fields, files) => {
      if (err) {
        res.writeHead(400, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ error: 'Parse error' }));
        return;
      }
      const audioFile = files.audio;
      if (!audioFile) {
        res.writeHead(400, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ error: 'No audio' }));
        return;
      }
      // Queue voice job too
      const transcript = 'صدای ضبط شده';
      const jobId = createJob({ message: `از صدا: ${transcript}`, games: fields.games ? JSON.parse(fields.games[0]) : ['snake'] });
      processJob(jobs.get(jobId));
      res.writeHead(200, { 'Content-Type': 'application/json' });
      res.end(JSON.stringify({ jobId, status: 'queued' }));
      try { fs.unlinkSync(audioFile[0].filepath); } catch (e) {}
    });
    return;
  }

  res.writeHead(404);
  res.end('Not found');
});

// Cleanup old jobs every 5 minutes
setInterval(() => {
  const now = Date.now();
  for (const [id, job] of jobs) {
    if (now - job.created > 300000) jobs.delete(id);
  }
}, 300000);

server.listen(PORT, '0.0.0.0', () => {
  console.log(`Chatbot API running on port ${PORT}`);
});