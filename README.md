# line-slack

## Features

- LINE Message <---> Slack Message
- LINE API call from Slack slash command

## Flow

- LINE app -> LINE Messaging API -> My Server -> Slack API
- Slack app (mention) -> Slack API -> My Server -> LINE Messaging API -> LINE app
- Slack app (slash command) -> Slack API -> My Server -> LINE Messaging API -> LINE app

## Settings

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