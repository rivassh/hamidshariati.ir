#!/bin/bash
# Process voice commands from queue
QUEUE="/root/nginx-certbot/websites/hamidshariati.ir/voice-tool/queue"
SESSION="voice-session"
TMUX="/usr/bin/tmux"

[ ! -d "$QUEUE" ] && exit 0

for f in "$QUEUE"/cmd_*.txt; do
    [ -f "$f" ] || continue
    cmd=$(cat "$f")
    rm -f "$f"
    
    [ -z "$cmd" ] && continue
    
    exists=$($TMUX has-session -t "$SESSION" 2>&1 && echo 1 || echo 0)
    [ "$exists" != "1" ] && $TMUX new-session -d -s "$SESSION"
    
    $TMUX send-keys -t "$SESSION" "$cmd" Enter
done
