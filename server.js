require('dotenv').config();
const http = require('http');
const { execSync } = require('child_process');
const { randomBytes } = require('crypto');
const fs = require('fs');
const path = require('path');

const PORT = process.env.PORT || 3457;
const TMUX_SESSION = process.env.TMUX_SESSION || '2';
const TMUX_WINDOW = process.env.TMUX_WINDOW || '0';
const BOT_NAME = process.env.BOT_NAME || 'دانا';
const TOPIC = process.env.TOPIC || 'games';
const TOPIC_TITLE = process.env.TOPIC_TITLE || 'چت‌بات';
const TOPIC_ICON = process.env.TOPIC_ICON || '🤖';
const SIDEBAR_ITEMS = JSON.parse(process.env.SIDEBAR_ITEMS || '[]');
const QUICK_ACTIONS = JSON.parse(process.env.QUICK_ACTIONS || '[]');

const jobs = new Map();

function createJob(data) {
  const id = randomBytes(8).toString('hex');
  jobs.set(id, {
    id, message: data.message, status: 'queued', response: null,
    created: Date.now(), beforeLines: 0
  });
  return id;
}

function processJob(job) {
  job.status = 'processing';
  try {
    const check = execSync(`tmux has-session -t ${TMUX_SESSION} 2>&1 && echo 'active' || echo 'inactive'`, { encoding: 'utf8', timeout: 5000 }).trim();
    if (check !== 'active') { job.status = 'error'; job.response = `${BOT_NAME} در دسترس نیست.`; return; }

    const prompt = `[WEBAPP:${TOPIC}]\n${job.message}\n\nفقط فارسی ساده جواب بده.`;
    job.beforeLines = parseInt(execSync(`tmux capture-pane -t ${TMUX_SESSION}:${TMUX_WINDOW} -p | wc -l`, { encoding: 'utf8' }).trim());

    const tmpFile = `/tmp/job-${job.id}.txt`;
    fs.writeFileSync(tmpFile, prompt, 'utf8');
    execSync(`tmux load-buffer ${tmpFile} && tmux paste-buffer -t ${TMUX_SESSION}:${TMUX_WINDOW} -d && tmux send-keys -t ${TMUX_SESSION}:${TMUX_WINDOW} Enter`);
    try { fs.unlinkSync(tmpFile); } catch (e) {}

    job.status = 'waiting';
    pollJob(job);
  } catch (e) { job.status = 'error'; job.response = `خطا: ${e.message}`; }
}

function pollJob(job, attempt = 0) {
  if (attempt >= 60) { job.status = 'timeout'; job.response = 'پاسخی دریافت نشد.'; return; }
  setTimeout(() => {
    try {
      const content = execSync(`tmux capture-pane -t ${TMUX_SESSION}:${TMUX_WINDOW} -p`, { encoding: 'utf8' }).trim();
      const lines = content.split('\n').slice(job.beforeLines);
      let response = lines.join('\n').trim().replace(/\x1b\[[0-9;]*m/g, '').replace(/[\x00-\x08\x0B\x0C\x0E-\x1F]/g, '').trim();

      if (response && response.length > 5 && !response.includes('[WEBAPP') && response !== job.lastContent) {
        job.lastContent = response;
        job.stableCount = (job.stableCount || 0) + 1;
        if (job.stableCount >= 2) { job.status = 'done'; job.response = response; return; }
      } else if (response) { job.lastContent = response; job.stableCount = 0; }
      pollJob(job, attempt + 1);
    } catch (e) { pollJob(job, attempt + 1); }
  }, 1000);
}

const server = http.createServer(async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
  if (req.method === 'OPTIONS') { res.writeHead(200); res.end(); return; }

  // Config
  if (req.method === 'GET' && req.url === '/api/config') {
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ botName: BOT_NAME, topic: TOPIC, topicTitle: TOPIC_TITLE, topicIcon: TOPIC_ICON, sidebarItems: SIDEBAR_ITEMS, quickActions: QUICK_ACTIONS }));
    return;
  }

  // Health
  if (req.method === 'GET' && req.url === '/api/health') {
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ status: 'ok' }));
    return;
  }

  // Chat
  if (req.method === 'POST' && req.url === '/api/chat') {
    let body = '';
    req.on('data', chunk => { body += chunk; });
    req.on('end', () => {
      try {
        const data = JSON.parse(body);
        if (!data.message) { res.writeHead(400, { 'Content-Type': 'application/json' }); res.end(JSON.stringify({ error: 'Message required' })); return; }
        const jobId = createJob(data);
        processJob(jobs.get(jobId));
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ jobId, status: 'queued' }));
      } catch (e) { res.writeHead(400, { 'Content-Type': 'application/json' }); res.end(JSON.stringify({ error: 'Invalid JSON' })); }
    });
    return;
  }

  // Status
  const statusMatch = req.url.match(/^\/api\/status\/([a-f0-9]+)$/);
  if (req.method === 'GET' && statusMatch) {
    const job = jobs.get(statusMatch[1]);
    if (!job) { res.writeHead(404, { 'Content-Type': 'application/json' }); res.end(JSON.stringify({ error: 'Not found' })); return; }
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ id: job.id, status: job.status, response: job.response }));
    return;
  }

  // Static files
  if (req.method === 'GET') {
    let filePath = req.url === '/' ? '/index.html' : req.url;
    filePath = path.join(__dirname, filePath);
    if (fs.existsSync(filePath) && fs.statSync(filePath).isFile()) {
      const ext = path.extname(filePath);
      const types = { '.html': 'text/html', '.js': 'application/javascript', '.css': 'text/css', '.json': 'application/json', '.svg': 'image/svg+xml' };
      res.writeHead(200, { 'Content-Type': types[ext] || 'text/plain' });
      fs.createReadStream(filePath).pipe(res);
      return;
    }
  }

  res.writeHead(404);
  res.end('Not found');
});

setInterval(() => {
  const now = Date.now();
  for (const [id, job] of jobs) { if (now - job.created > 300000) jobs.delete(id); }
}, 300000);

server.listen(PORT, '0.0.0.0', () => {
  console.log(`WebApp running on port ${PORT} [${TOPIC}]`);
});