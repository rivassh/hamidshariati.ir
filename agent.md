# Agent Instructions

## Project Structure
This is a flexible chatbot web app that can be configured for different topics.

## Configuration
Edit `.env` to customize:
- `TOPIC` - Topic type (games, religion, education, custom)
- `BOT_NAME` - Bot name in Persian
- `SIDEBAR_ITEMS` - JSON array of sidebar links
- `QUICK_ACTIONS` - JSON array of quick action buttons

## When Starting
1. Read `.env` to understand the current topic
2. Ask user what topic they want if not configured
3. Update `.env` with their choices
4. Update sidebar items based on topic

## Topic Examples

### Games
```env
TOPIC=games
SIDEBAR_ITEMS=[{"icon":"🐍","name":"مار نئون","url":"/game-platform/"},{"icon":"🎲","name":"بازی کلمات","url":"/games/game2/web/"}]
```

### Religion
```env
TOPIC=religion
SIDEBAR_ITEMS=[{"icon":"📖","name":"احکام","url":"/rules/"},{"icon":"🕌","name":"نماز","url":"/prayer/"},{"icon":"📿","name":"ذکر","url":"/dhikr/"}]
```

### Education
```env
TOPIC=education
SIDEBAR_ITEMS=[{"icon":"📚","name":"ریاضی","url":"/math/"},{"icon":"🔬","name":"علوم","url":"/science/"}]
```

## Git Workflow
- All changes commit to: `/opt/webapp.git`
- Clone: `git clone /opt/webapp.git`
- Push: `git push origin main`

## API Endpoints
- `GET /api/config` - Get topic configuration
- `POST /api/chat` - Send message (returns jobId)
- `GET /api/status/:jobId` - Poll for response

## Notes
- Bug reports stored in localStorage (separate from chat history)
- Chat history stored in localStorage per browser
- Voice input uses Web Speech API (no API key needed)
- PWA support for mobile install