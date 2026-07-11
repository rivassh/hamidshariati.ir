#!/bin/bash
# Check for new game upgrade requests and run via mimo

QUEUE_DIR="/root/nginx-certbot/websites/hamidshariati.ir/games/queue"
PROCESSED_FILE="/root/nginx-certbot/websites/hamidshariati.ir/games/queue/.processed"
LOG_FILE="/root/nginx-certbot/websites/hamidshariati.ir/games/queue/cron.log"
PROJECT_DIR="/root/nginx-certbot/websites/hamidshariati.ir/games"
MIMO="/root/.mimocode/bin/mimo"

touch "$PROCESSED_FILE"

for req_file in "$QUEUE_DIR"/req_*.json; do
    [ -f "$req_file" ] || continue
    
    req_id=$(basename "$req_file" .json)
    
    if grep -q "$req_id" "$PROCESSED_FILE" 2>/dev/null; then
        continue
    fi
    
    echo "[$(date)] New request: $req_id" >> "$LOG_FILE"
    
    games=$(jq -r '.games | join(", ")' "$req_file" 2>/dev/null)
    change_type=$(jq -r '.changeType' "$req_file" 2>/dev/null)
    description=$(jq -r '.description' "$req_file" 2>/dev/null)
    priority=$(jq -r '.priority' "$req_file" 2>/dev/null)
    notes=$(jq -r '.notes // ""' "$req_file" 2>/dev/null)
    
    game_names=""
    for g in $games; do
        case $g in
            snake) game_names="$game_names مار نئون (/game-platform/)" ;;
            game1) game_names="$game_names بازی ۱ (/games/game1/)" ;;
            game2) game_names="$game_names بازی ۲ (/games/game2/)" ;;
        esac
    done
    
    case $change_type in
        feature) type_label="ویژگی جدید" ;;
        bugfix) type_label="رفع باگ" ;;
        enhancement) type_label="بهبود عملکرد" ;;
        visual) type_label="تغییرات بصری" ;;
        gameplay) type_label="تغییر گیم‌پلی" ;;
        *) type_label="سایر" ;;
    esac
    
    message="🎮 درخواست ارتقاء بازی جدید!
━━━━━━━━━━━━━━━━━━━━━━━━
بازی: $game_names
نوع: $type_label
اولویت: $priority
━━━━━━━━━━━━━━━━━━━━━━━━
توضیحات:
$description"

    if [ -n "$notes" ]; then
        message="$message

نکات: $notes"
    fi

    message="$message

━━━━━━━━━━━━━━━━━━━━━━━━
فایل درخواست: $req_file
برای اجرا، فایل رو بخون و تغییرات رو اعمال کن."

    # Mark as processed before running to prevent race condition
    echo "$req_id" >> "$PROCESSED_FILE"

    # Update status to processing
    jq --arg ts "$(date -Iseconds)" '. + {status: "processing", started: $ts}' "$req_file" > "${req_file}.tmp" && mv "${req_file}.tmp" "$req_file"

    echo "[$(date)] Running mimo for $req_id" >> "$LOG_FILE"
    nohup bash -c "
        \"$MIMO\" run --dangerously-skip-permissions --dir \"$PROJECT_DIR\" \"$message\" >> \"$LOG_FILE\" 2>&1
        exit_code=\$?
        if [ \$exit_code -eq 0 ]; then
            jq --arg ts \"\$(date -Iseconds)\" '. + {status: \"done\", finished: \$ts}' \"$req_file\" > \"${req_file}.tmp\" && mv \"${req_file}.tmp\" \"$req_file\"
        else
            jq --arg ts \"\$(date -Iseconds)\" --arg err \"exit \$exit_code\" '. + {status: \"failed\", finished: \$ts, error: \$err}' \"$req_file\" > \"${req_file}.tmp\" && mv \"${req_file}.tmp\" \"$req_file\"
        fi
    " >> "$LOG_FILE" 2>&1 &
    
    echo "[$(date)] Started mimo for $req_id (PID: $!)" >> "$LOG_FILE"
done
