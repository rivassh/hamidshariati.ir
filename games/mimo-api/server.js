require('dotenv').config();
const http = require('http');
const { randomBytes } = require('crypto');
const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const PORT = 3456;
const BOT_NAME = process.env.BOT_NAME || 'دانا';
const ADMIN_TOKEN = process.env.ADMIN_TOKEN || 'admin123';
const QUEUE_DIR = '/root/nginx-certbot/websites/hamidshariati.ir/games/queue';
const LOG_FILE = '/tmp/chatbot-jobs.json';

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

// Write message to queue file for cron to process
function writeToQueue(job) {
  const queueFile = path.join(QUEUE_DIR, `chatbot_${job.id}.json`);
  const queueData = {
    id: job.id,
    source: 'chatbot',
    games: job.games || ['snake'],
    changeType: 'chat',
    description: job.message,
    priority: 'normal',
    notes: '',
    status: 'queued',
    created: new Date().toISOString(),
    session: currentSession
  };
  fs.writeFileSync(queueFile, JSON.stringify(queueData, null, 2));
  return queueFile;
}

// Check if queue file has response
function checkQueueResponse(jobId) {
  const queueFile = path.join(QUEUE_DIR, `chatbot_${jobId}.json`);
  if (!fs.existsSync(queueFile)) return null;
  
  try {
    const data = JSON.parse(fs.readFileSync(queueFile, 'utf8'));
    return {
      status: data.status,
      response: data.response || data.error || null,
      finished: data.finished
    };
  } catch (e) {
    return null;
  }
}

function createJob(data) {
  const id = randomBytes(8).toString('hex');
  const job = {
    id,
    message: data.message,
    games: data.games || ['snake'],
    session: currentSession,
    status: 'queued',
    response: null,
    created: Date.now()
  };
  jobs.set(id, job);
  
  // Write to queue for processing
  writeToQueue(job);
  
  return job;
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

// Poll queue for response
function pollForResponse(job) {
  let attempts = 0;
  const maxAttempts = 120; // 2 minutes
  
  const check = () => {
    if (attempts >= maxAttempts) {
      job.status = 'timeout';
      job.response = 'پاسخی دریافت نشد.';
      saveJobs();
      return;
    }
    
    const result = checkQueueResponse(job.id);
    if (result && result.status === 'done') {
      job.status = 'done';
      job.response = result.response || 'انجام شد.';
      saveJobs();
      return;
    }
    
    if (result && result.status === 'failed') {
      job.status = 'error';
      job.response = result.response || 'خطا در پردازش.';
      saveJobs();
      return;
    }
    
    attempts++;
    setTimeout(check, 1000);
  };
  
  check();
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
        if (!data.message) {
          res.writeHead(400, { 'Content-Type': 'application/json' });
          res.end(JSON.stringify({ error: 'Message required' }));
          return;
        }
        
        const job = createJob(data);
        pollForResponse(job);
        
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ jobId: job.id, status: 'queued', session: currentSession }));
      } catch (e) {
        res.writeHead(400, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ error: 'Invalid JSON' }));
      }
    });
    return;
  }

  const statusMatch = req.url.match(/^\/status\/([a-f0-9]+)$/);
  if (req.method === 'GET' && statusMatch) {
    const job = jobs.get(statusMatch[1]);
    if (!job) {
      res.writeHead(404, { 'Content-Type': 'application/json' });
      res.end(JSON.stringify({ error: 'Not found' }));
      return;
    }
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ id: job.id, status: job.status, response: job.response }));
    return;
  }

  // Admin endpoints
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
  for (const [id, job] of jobs) {
    if (now - job.created > 300000) jobs.delete(id);
  }
}, 300000);

server.listen(PORT, '0.0.0.0', () => {
  console.log(`[${BOT_NAME}] Chatbot API running on port ${PORT} | Session: ${currentSession}`);
  console.log(`Queue dir: ${QUEUE_DIR}`);
});
