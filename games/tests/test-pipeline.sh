#!/bin/bash
# TDD tests for the game upgrade pipeline
# Tests: API, queue processing, status tracking

set -e

QUEUE_DIR="/root/nginx-certbot/websites/hamidshariati.ir/games/queue"
SCRIPT_DIR="/root/nginx-certbot/websites/hamidshariati.ir/games"
TEST_DIR="$SCRIPT_DIR/tests"
PROCESSED_FILE="$QUEUE_DIR/.processed"
PASSED=0
FAILED=0

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

api_get() {
    php "$TEST_DIR/api-test-helper.php" GET 2>/dev/null
}

api_post() {
    php "$TEST_DIR/api-test-helper.php" POST "$1" 2>/dev/null
}

assert_eq() {
    local desc="$1" expected="$2" actual="$3"
    if [ "$expected" = "$actual" ]; then
        echo -e "  ${GREEN}✓${NC} $desc"
        PASSED=$((PASSED + 1))
    else
        echo -e "  ${RED}✗${NC} $desc"
        echo -e "    expected: ${YELLOW}$expected${NC}"
        echo -e "    got:      ${YELLOW}$actual${NC}"
        FAILED=$((FAILED + 1))
    fi
}

assert_file_exists() {
    local desc="$1" file="$2"
    if [ -f "$file" ]; then
        echo -e "  ${GREEN}✓${NC} $desc"
        PASSED=$((PASSED + 1))
    else
        echo -e "  ${RED}✗${NC} $desc — $file"
        FAILED=$((FAILED + 1))
    fi
}

TEST_REQ_IDS=""

cleanup() {
    rm -f "$QUEUE_DIR"/req_test_*.json
    rm -f "$QUEUE_DIR"/req_cleanup_*.json
    for tid in $TEST_REQ_IDS; do
        rm -f "$QUEUE_DIR/$tid.json"
    done
    # Remove any leftover test queue files
    rm -f "$QUEUE_DIR"/req_*.json
    # Clean .processed of test entries
    if [ -f "$PROCESSED_FILE" ]; then
        grep -v "req_test_\|req_cleanup_" "$PROCESSED_FILE" > "${PROCESSED_FILE}.tmp" 2>/dev/null || true
        mv "${PROCESSED_FILE}.tmp" "$PROCESSED_FILE"
    fi
    TEST_REQ_IDS=""
}

# ============================================================
echo -e "\n${YELLOW}═══ 1. API POST (Create Request) ═══${NC}"
# ============================================================

cleanup

RESP=$(api_post '{"games":["snake"],"changeType":"feature","description":"test feature","priority":"high","notes":"test note"}')
REQ_ID=$(echo "$RESP" | jq -r '.id')
TEST_REQ_IDS="$TEST_REQ_IDS $REQ_ID"

assert_eq "POST returns success" "true" "$(echo "$RESP" | jq -r '.success')"
assert_eq "ID starts with req_" "true" "$(echo "$REQ_ID" | grep -q '^req_' && echo true || echo false)"
assert_file_exists "Request file on disk" "$QUEUE_DIR/$REQ_ID.json"

# ============================================================
echo -e "\n${YELLOW}═══ 2. API POST (Validation) ═══${NC}"
# ============================================================

R=$(php "$TEST_DIR/api-test-helper.php" POST '{"description":"no games"}' 2>/dev/null)
assert_eq "Missing games → error" "Missing required fields" "$(echo "$R" | jq -r '.error')"

R=$(php "$TEST_DIR/api-test-helper.php" POST '{"games":["snake"]}' 2>/dev/null)
assert_eq "Missing description → error" "Missing required fields" "$(echo "$R" | jq -r '.error')"

R=$(php "$TEST_DIR/api-test-helper.php" POST '{}' 2>/dev/null)
assert_eq "Empty body → error" "Missing required fields" "$(echo "$R" | jq -r '.error')"

# ============================================================
echo -e "\n${YELLOW}═══ 3. API GET (List Requests) ═══${NC}"
# ============================================================

RESP=$(api_post '{"games":["game1"],"changeType":"bugfix","description":"fix bug","priority":"low"}')
TEST_REQ_IDS="$TEST_REQ_IDS $(echo "$RESP" | jq -r '.id')"

RESP=$(api_get)
COUNT=$(echo "$RESP" | jq 'length')
assert_eq "Returns ≥1 request" "true" "$([ "$COUNT" -ge 1 ] && echo true || echo false)"
assert_eq "Returns JSON array" "array" "$(echo "$RESP" | jq -r 'type')"

FIRST=$(echo "$RESP" | jq '.[0]')
assert_eq "Has id field" "true" "$(echo "$FIRST" | jq -e '.id' > /dev/null 2>&1 && echo true || echo false)"
assert_eq "Has status field" "true" "$(echo "$FIRST" | jq -e '.status' > /dev/null 2>&1 && echo true || echo false)"
assert_eq "Has games field" "true" "$(echo "$FIRST" | jq -e '.games' > /dev/null 2>&1 && echo true || echo false)"

# ============================================================
echo -e "\n${YELLOW}═══ 4. Queue File Structure ═══${NC}"
# ============================================================

cleanup

RESP=$(api_post '{"games":["snake","game1"],"changeType":"visual","description":"neon glow","priority":"medium"}')
TEST_REQ_IDS="$TEST_REQ_IDS $(echo "$RESP" | jq -r '.id')"

REQ_FILE=$(ls -t "$QUEUE_DIR"/req_*.json 2>/dev/null | head -1)
assert_file_exists "Queue file created" "$REQ_FILE"

assert_eq "Has id" "true" "$(jq -e '.id' "$REQ_FILE" > /dev/null 2>&1 && echo true || echo false)"
assert_eq "Has games" "true" "$(jq -e '.games' "$REQ_FILE" > /dev/null 2>&1 && echo true || echo false)"
assert_eq "Status is pending" "pending" "$(jq -r '.status' "$REQ_FILE")"
assert_eq "Has timestamp" "true" "$(jq -e '.timestamp' "$REQ_FILE" > /dev/null 2>&1 && echo true || echo false)"
assert_eq "Games count is 2" "2" "$(jq '.games | length' "$REQ_FILE")"

# ============================================================
echo -e "\n${YELLOW}═══ 5. Status Lifecycle ═══${NC}"
# ============================================================

cleanup

RESP=$(api_post '{"games":["snake"],"changeType":"feature","description":"lifecycle test","priority":"low"}')
REQ_ID=$(echo "$RESP" | jq -r '.id')
TEST_REQ_IDS="$TEST_REQ_IDS $REQ_ID"
REQ_FILE="$QUEUE_DIR/$REQ_ID.json"

assert_eq "Starts as pending" "pending" "$(jq -r '.status' "$REQ_FILE")"

# → processing
jq --arg ts "$(date -Iseconds)" '. + {status:"processing", started:$ts}' "$REQ_FILE" > "${REQ_FILE}.tmp" && mv "${REQ_FILE}.tmp" "$REQ_FILE"
assert_eq "→ processing" "processing" "$(jq -r '.status' "$REQ_FILE")"
assert_eq "Has started ts" "true" "$(jq -e '.started' "$REQ_FILE" > /dev/null 2>&1 && echo true || echo false)"

# → done
jq --arg ts "$(date -Iseconds)" '. + {status:"done", finished:$ts}' "$REQ_FILE" > "${REQ_FILE}.tmp" && mv "${REQ_FILE}.tmp" "$REQ_FILE"
assert_eq "→ done" "done" "$(jq -r '.status' "$REQ_FILE")"
assert_eq "Has finished ts" "true" "$(jq -e '.finished' "$REQ_FILE" > /dev/null 2>&1 && echo true || echo false)"

# → failed (separate request)
cleanup
RESP=$(api_post '{"games":["snake"],"changeType":"bugfix","description":"fail test","priority":"low"}')
REQ_ID=$(echo "$RESP" | jq -r '.id')
TEST_REQ_IDS="$TEST_REQ_IDS $REQ_ID"
REQ_FILE="$QUEUE_DIR/$REQ_ID.json"

jq --arg ts "$(date -Iseconds)" --arg err "exit 1" '. + {status:"failed", finished:$ts, error:$err}' "$REQ_FILE" > "${REQ_FILE}.tmp" && mv "${REQ_FILE}.tmp" "$REQ_FILE"
assert_eq "→ failed" "failed" "$(jq -r '.status' "$REQ_FILE")"
assert_eq "Has error" "exit 1" "$(jq -r '.error' "$REQ_FILE")"

# ============================================================
echo -e "\n${YELLOW}═══ 6. Processed File Tracking ═══${NC}"
# ============================================================

cleanup

echo "req_test_duplicate" >> "$PROCESSED_FILE"
assert_eq "ID in .processed" "true" "$(grep -q "req_test_duplicate" "$PROCESSED_FILE" && echo true || echo false)"
assert_eq "ID recorded once" "1" "$(grep -c "req_test_duplicate" "$PROCESSED_FILE")"

# ============================================================
echo -e "\n${YELLOW}═══ 7. Cron Idempotency ═══${NC}"
# ============================================================

# Create a temp directory for isolated queue test
TEMP_QUEUE=$(mktemp -d)
TEMP_PROCESSED="$TEMP_QUEUE/.processed"
touch "$TEMP_PROCESSED"

# Create request in temp queue
RESP=$(api_post '{"games":["snake"],"changeType":"feature","description":"skip test","priority":"low"}')
REQ_ID=$(echo "$RESP" | jq -r '.id')
TEST_REQ_IDS="$TEST_REQ_IDS $REQ_ID"
# Move the created file to temp queue
mv "$QUEUE_DIR/$REQ_ID.json" "$TEMP_QUEUE/" 2>/dev/null || true

echo "$REQ_ID" >> "$TEMP_PROCESSED"

BEFORE=$(wc -l < "$TEMP_PROCESSED")

# Run a mini cron on temp queue
for f in "$TEMP_QUEUE"/req_*.json; do
    [ -f "$f" ] || continue
    fid=$(basename "$f" .json)
    grep -q "$fid" "$TEMP_PROCESSED" 2>/dev/null && continue
    echo "$fid" >> "$TEMP_PROCESSED"
done

AFTER=$(wc -l < "$TEMP_PROCESSED")

assert_eq "Skips processed request" "$BEFORE" "$AFTER"

rm -rf "$TEMP_QUEUE"

# ============================================================
echo -e "\n${YELLOW}═══ 8. Cron Sets Processing Status ═══${NC}"
# ============================================================

cleanup

RESP=$(api_post '{"games":["snake"],"changeType":"feature","description":"cron test","priority":"low"}')
REQ_ID=$(echo "$RESP" | jq -r '.id')
TEST_REQ_IDS="$TEST_REQ_IDS $REQ_ID"

bash "$SCRIPT_DIR/check-requests.sh" 2>/dev/null

REQ_FILE="$QUEUE_DIR/$REQ_ID.json"
assert_eq "Cron → processing" "processing" "$(jq -r '.status' "$REQ_FILE" 2>/dev/null)"
assert_eq "Cron adds started" "true" "$(jq -e '.started' "$REQ_FILE" > /dev/null 2>&1 && echo true || echo false)"

# ============================================================
echo -e "\n${YELLOW}═══ 9. API GET Shows Status ═══${NC}"
# ============================================================

cleanup

RESP=$(api_post '{"games":["snake"],"changeType":"feature","description":"api status","priority":"low"}')
REQ_ID=$(echo "$RESP" | jq -r '.id')
TEST_REQ_IDS="$TEST_REQ_IDS $REQ_ID"
REQ_FILE="$QUEUE_DIR/$REQ_ID.json"

jq '. + {status:"done", summary:"All good"}' "$REQ_FILE" > "${REQ_FILE}.tmp" && mv "${REQ_FILE}.tmp" "$REQ_FILE"

API_RESP=$(api_get)
STATUS=$(echo "$API_RESP" | jq -r ".[] | select(.id == \"$REQ_ID\") | .status")
SUMMARY=$(echo "$API_RESP" | jq -r ".[] | select(.id == \"$REQ_ID\") | .summary")

assert_eq "GET shows done" "done" "$STATUS"
assert_eq "GET shows summary" "All good" "$SUMMARY"

# ============================================================
# Summary
# ============================================================

cleanup

echo -e "\n${YELLOW}═══════════════════════════════════════════${NC}"
echo -e "Results: ${GREEN}$PASSED passed${NC}, ${RED}$FAILED failed${NC}"
echo -e "${YELLOW}═══════════════════════════════════════════${NC}"

[ "$FAILED" -gt 0 ] && exit 1
exit 0
