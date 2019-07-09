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
    $response = $bot->replyMessage(
        $event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('OK!!')
    );

    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {
//        $slackBlock = new \Slack\SlackBlock();
//        $slackBlock
//            ->addBlock(
//                \Slack\SlackBlock\Section::text('plain_text', $event->getText())
//            );
//        $slack = new \Slack\Slack(
//            getenv('SLACK_WEBHOOK_URL')
//        );
//        $slack->send($slackBlock);

        $slackText = new \Slack\SlackText($event->getText());

        $slack = new \Slack\Slack(
            'https://slack.com/api/chat.postMessage',
            getenv('SLACK_CHANNEL'),
            null,
            getenv('SLACK_BOT_OAUTH_TOKEN')
        );
        $slack->post($slackText);
    }
}