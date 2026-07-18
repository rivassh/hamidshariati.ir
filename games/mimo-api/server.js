require('dotenv').config();
const http = require('http');
const { randomBytes } = require('crypto');

const PORT = 3456;
const BOT_NAME = process.env.BOT_NAME || 'دانا';

const jobs = new Map();

function createJob(data) {
  const id = randomBytes(8).toString('hex');
  jobs.set(id, { id, message: data.message, status: 'queued', response: null, created: Date.now() });
  return id;
}

function processJob(job) {
  job.status = 'processing';
  
  // Simulate processing
  setTimeout(() => {
    const msg = job.message.toLowerCase();
    let response = '';
    
    if (msg.includes('سلام') || msg.includes('حال')) {
      response = `سلام! خوبم ممنون. ${BOT_NAME} در خدمتم. چه کمکی می‌خوای؟`;
    } else if (msg.includes('شطرنج')) {
      response = 'شطرنج ساختم! به صفحه بازی‌ها برو و شطرنج رو انتخاب کن. ♟️';
    } else if (msg.includes('بازی جدید') || msg.includes('بازی بساز')) {
      response = 'چه بازی‌ای می‌خوای؟ مثلاً:\n• بازی فکری\n• بازی ماجراجویی\n• بازی ورزشی\n\nبگو تا بسازمش!';
    } else if (msg.includes('قدرت ویژه')) {
      response = 'قدرت ویژه اضافه شد! حالا می‌تونی از اون استفاده کنی.';
    } else if (msg.includes('صدا')) {
      response = 'صدا اضافه شد! حالا بازی صدا داره.';
    } else if (msg.includes('تم جدید')) {
      response = 'تم جدید اعمال شد! رنگ‌ها عوض شدن.';
    } else if (msg.includes('باگ') || msg.includes('خطا')) {
      response = 'باگ گزارش شد. در اسرع وقت رفع می‌کنم.';
    } else if (msg.includes('موبایل')) {
      response = 'بازی موبایل‌فرندلی شد! حالا راحت‌تر بازی کن.';
    } else {
      response = `درخواستت دریافت شد: "${job.message.slice(0, 50)}..."\n\nدارم روش کار می‌کنم! 🎮`;
    }
    
    job.status = 'done';
    job.response = response;
  }, 1500);
}

const server = http.createServer(async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
  if (req.method === 'OPTIONS') { res.writeHead(200); res.end(); return; }

  if (req.method === 'GET' && req.url === '/health') {
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ status: 'ok' }));
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
        res.end(JSON.stringify({ jobId, status: 'queued' }));
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

  res.writeHead(404);
  res.end('Not found');
});

setInterval(() => {
  const now = Date.now();
  for (const [id, job] of jobs) { if (now - job.created > 300000) jobs.delete(id); }
}, 300000);

server.listen(PORT, '0.0.0.0', () => {
  console.log(`Chatbot API running on port ${PORT}`);
});