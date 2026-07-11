#!/usr/bin/env python3
"""Simple API server for game upgrade requests."""

import json
import os
import sys
from http.server import HTTPServer, BaseHTTPRequestHandler
from datetime import datetime
import uuid

QUEUE_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'queue')
PORT = 8099

os.makedirs(QUEUE_DIR, exist_ok=True)


class RequestHandler(BaseHTTPRequestHandler):
    def do_OPTIONS(self):
        self.send_response(200)
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type')
        self.end_headers()

    def do_GET(self):
        if self.path == '/api.php' or self.path == '/':
            requests = []
            for f in os.listdir(QUEUE_DIR):
                if f.startswith('req_') and f.endswith('.json'):
                    filepath = os.path.join(QUEUE_DIR, f)
                    try:
                        with open(filepath, 'r') as fh:
                            data = json.load(fh)
                            requests.append(data)
                    except:
                        pass

            requests.sort(key=lambda x: x.get('timestamp', ''), reverse=True)

            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(json.dumps(requests, ensure_ascii=False).encode())
        else:
            self.send_error(404)

    def do_POST(self):
        if self.path == '/api.php' or self.path == '/':
            content_length = int(self.headers.get('Content-Length', 0))
            body = self.rfile.read(content_length)

            try:
                data = json.loads(body)
            except:
                self.send_response(400)
                self.send_header('Content-Type', 'application/json')
                self.end_headers()
                self.wfile.write(json.dumps({'error': 'Invalid JSON'}).encode())
                return

            if not data.get('games') or not data.get('description'):
                self.send_response(400)
                self.send_header('Content-Type', 'application/json')
                self.end_headers()
                self.wfile.write(json.dumps({'error': 'Missing required fields'}).encode())
                return

            request_id = f"req_{uuid.uuid4().hex[:12]}"
            request = {
                'id': request_id,
                'timestamp': datetime.now().isoformat(),
                'games': data['games'],
                'changeType': data.get('changeType', 'feature'),
                'description': data['description'],
                'priority': data.get('priority', 'medium'),
                'notes': data.get('notes', ''),
                'status': 'pending'
            }

            filepath = os.path.join(QUEUE_DIR, f"{request_id}.json")
            with open(filepath, 'w') as f:
                json.dump(request, f, ensure_ascii=False, indent=2)

            print(f"[{datetime.now()}] New request: {request_id}")

            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(json.dumps({'success': True, 'id': request_id}).encode())
        else:
            self.send_error(404)

    def log_message(self, format, *args):
        print(f"[{datetime.now()}] {args[0]}")


if __name__ == '__main__':
    server = HTTPServer(('127.0.0.1', PORT), RequestHandler)
    print(f"Game upgrade API running on http://127.0.0.1:{PORT}")
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        print("\nShutting down...")
        server.shutdown()
