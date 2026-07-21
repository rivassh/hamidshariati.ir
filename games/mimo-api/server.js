require('dotenv').config();
const http = require('http');
const { randomBytes } = require('crypto');
const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const PORT = 3456;
const BOT_NAME = process.env.BOT_NAME || 'دانا';
const ADMIN_TOKEN = process.env.ADMIN_TOKEN || 'admin123';
const LOG_FILE = '/tmp/chatbot-jobs.json';

// Mutable session - can be changed at runtime
let currentSession = process.env.TMUX_SESSION || '0';
let currentWindow = process.env.TMUX_WINDOW || '0';

const jobs = new Map();
let recentJobs = [];

function loadJobs() {
  try {
    if (fs.existsSync(LOG_FILE)) {
      recentJobs = JSON.parse(fs.readFileSync(LOG_FILE, 'utf8'));
    }
  } catch (e) { recentJobs = []; }
}

function saveJobs() {
  recentJobs = Array.from(jobs.values()).slice(-50);
  fs.writeFileSync(LOG_FILE, JSON.stringify(recentJobs, null, 2));
}

loadJobs();

function createJob(data) {
  const id = randomBytes(8).toString('hex');
  jobs.set(id, { id, message: data.message, session: currentSession, status: 'queued', response: null, created: Date.now() });
  return id;
}

function getTmuxSessions() {
  try {
    const output = execSync('tmux list-sessions 2>/dev/null', { encoding: 'utf8' });
    return output.trim().split('\n').map(line => {
      const match = line.match(/^(\d+):/);
      return match ? { id: match[1], info: line.trim() } : null;
    }).filter(Boolean);
  } catch (e) { return []; }
}

function sendToTmux(session, message) {
  try {
    const tmpFile = `/tmp/chatbot-cmd-${Date.now()}.txt`;
    fs.writeFileSync(tmpFile, message, 'utf8');
    execSync(`tmux load-buffer ${tmpFile} && tmux paste-buffer -t ${session}:0 -d && tmux send-keys -t ${session}:0 Enter`);
    try { fs.unlinkSync(tmpFile); } catch (e) {}
    return true;
  } catch (e) { return false; }
}

function processJob(job) {
  job.status = 'processing';
  
  const sent = sendToTmux(job.session, job.message);
  
  if (!sent) {
    job.status = 'done';
    job.response = `${BOT_NAME} در دسترس نیست. لطفاً session رو چک کن.`;
    saveJobs();
    return;
  }

  // Wait and capture response from tmux
  let attempts = 0;
  const beforeLines = parseInt(
    execSync(`tmux capture-pane -t ${job.session}:0 -p 2>/dev/null | wc -l`, { encoding: 'utf8' }).trim() || '0'
  );

  const checkResponse = () => {
    if (attempts >= 30) {
      job.status = 'done';
      job.response = 'پاسخی دریافت نشد. لطفاً بررسی کن.';
      saveJobs();
      return;
    }

    setTimeout(() => {
      try {
        const content = execSync(`tmux capture-pane -t ${job.session}:0 -p 2>/dev/null`, { encoding: 'utf8' }).trim();
        const lines = content.split('\n');
        const newLines = lines.slice(beforeLines);
        let response = newLines.join('\n').trim()
          .replace(/\x1b\[[0-9;]*m/g, '')
          .replace(/[\x00-\x08\x0B\x0C\x0E-\x1F]/g, '')
          .trim();

        if (response && response.length > 3 && response !== job.lastContent) {
          job.lastContent = response;
          job.stableCount = (job.stableCount || 0) + 1;
          if (job.stableCount >= 2) {
            job.status = 'done';
            job.response = response;
            saveJobs();
            return;
          }
        } else if (response) {
          job.lastContent = response;
          job.stableCount = 0;
        }
        attempts++;
        checkResponse();
      } catch (e) {
        attempts++;
        checkResponse();
      }
    }, 1000);
  };

  checkResponse();
}

function checkAuth(req) {
  const url = new URL(req.url, `http://localhost`);
  const token = url.searchParams.get('token');
  return token === ADMIN_TOKEN;
}

const server = http.createServer(async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
  if (req.method === 'OPTIONS') { res.writeHead(200); res.end(); return; }

  // Public endpoints
  if (req.method === 'GET' && req.url === '/health') {
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ status: 'ok' }));
    return;
  }

  if (req.method === 'GET' && req.url === '/jobs') {
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ jobs: recentJobs, count: recentJobs.length }));
    return;
  }

  if (req.method === 'GET' && req.url === '/config') {
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ botName: BOT_NAME, topic: process.env.TOPIC || 'games' }));
    return;
  }

  if (req.method === 'POST' && req.url === '/chat') {
    let body = '';
    req.on('data', chunk => { body += chunk; });
    req.on('end', () => {
      try {
        const data = JSON.parse(body);
        if (!data.message) { res.writeHead(400, { 'Content-Type': 'application/json' }); res.end(JSON.stringify({ error: 'Message required' })); return; }
        const jobId = createJob(data);
        processJob(jobs.get(jobId));
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ jobId, status: 'queued', session: currentSession }));
      } catch (e) { res.writeHead(400, { 'Content-Type': 'application/json' }); res.end(JSON.stringify({ error: 'Invalid JSON' })); }
    });
    return;
  }

  const statusMatch = req.url.match(/^\/status\/([a-f0-9]+)$/);
  if (req.method === 'GET' && statusMatch) {
    const job = jobs.get(statusMatch[1]);
    if (!job) { res.writeHead(404, { 'Content-Type': 'application/json' }); res.end(JSON.stringify({ error: 'Not found' })); return; }
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ id: job.id, status: job.status, response: job.response }));
    return;
  }

  // Admin endpoints (require token)
  if (req.url.startsWith('/admin')) {
    if (!checkAuth(req)) {
      res.writeHead(401, { 'Content-Type': 'application/json' });
      res.end(JSON.stringify({ error: 'Unauthorized' }));
      return;
    }

    if (req.method === 'GET' && req.url.startsWith('/admin/config')) {
      res.writeHead(200, { 'Content-Type': 'application/json' });
      res.end(JSON.stringify({ botName: BOT_NAME, session: currentSession, window: currentWindow }));
      return;
    }

    if (req.method === 'GET' && req.url.startsWith('/admin/sessions')) {
      const sessions = getTmuxSessions();
      res.writeHead(200, { 'Content-Type': 'application/json' });
      res.end(JSON.stringify({ sessions, current: currentSession }));
      return;
    }

    if (req.method === 'POST' && req.url.startsWith('/admin/session')) {
      let body = '';
      req.on('data', chunk => { body += chunk; });
      req.on('end', () => {
        try {
          const data = JSON.parse(body);
          if (data.session) currentSession = String(data.session);
          if (data.window) currentWindow = String(data.window);
          res.writeHead(200, { 'Content-Type': 'application/json' });
          res.end(JSON.stringify({ session: currentSession, window: currentWindow }));
        } catch (e) {
          res.writeHead(400, { 'Content-Type': 'application/json' });
          res.end(JSON.stringify({ error: 'Invalid JSON' }));
        }
      });
      return;
    }

    if (req.method === 'GET' && req.url.startsWith('/admin/jobs')) {
      res.writeHead(200, { 'Content-Type': 'application/json' });
      res.end(JSON.stringify({ jobs: recentJobs, count: recentJobs.length }));
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
  console.log(`[${BOT_NAME}] Chatbot API running on port ${PORT} | Session: ${currentSession}`);
});
