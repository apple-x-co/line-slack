<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::create(__DIR__ . '/../');
$dotenv->load();

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('LINE_ACCESS_TOKEN'));
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('LINE_CHANNEL_SECRET')]);

$signature = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

$events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);

/** @var  $event \LINE\LINEBot\Event\MessageEvent */
foreach ($events as $event) {
    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {
        $contact_text = $event->getText();

        $chatPostMessage = new \Slack\ChatPostMessage(
            getenv('SLACK_CHANNEL'),
            getenv('SLACK_BOT_OAUTH_TOKEN')
        );
        $postResult = $chatPostMessage->post(
            new \Slack\MessageBuilder\MessageText($contact_text)
        );
        if ( ! $postResult->isOk()) {
            error_log($postResult->error());
        }

        if ($postResult->isOk()) {
            $chatUpdate = new \Slack\ChatUpdate(
                $postResult->get('channel'),
                getenv('SLACK_BOT_OAUTH_TOKEN')
            );
            $postResult = $chatUpdate->post(
                $postResult->get('ts'),
                new \Slack\MessageBuilder\MessageText($contact_text . "\n\n```ts:" . $postResult->get('ts') . '```')
            );
            if ( ! $postResult->isOk()) {
                error_log($postResult->error());
            }

            $response = $bot->replyMessage(
                $event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('OK!!')
            );
            continue;
        }
    }

    $response = $bot->replyMessage(
        $event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('???')
    );
}