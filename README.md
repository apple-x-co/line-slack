# line-slack

## settings

### LINE

Webhook : `/public/line/line.php`

### Slack

**Event Subscriptions**

Request URL: `/public/slack/event.php`

Subscribe to Bot Events: `app_mension`

**Slash Commands**

Command: `/lineslack`

Webhook: `/public/slack/command.php`

**OAuth Tokens & Redirect URLs**

Scopes:

CONVERSATIONS: `chat:write:bot`, `incoming-webhook`

INTERACTIVITY: `bot`, `commands`