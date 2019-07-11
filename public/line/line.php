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
    if ($event instanceof \LINE\LINEBot\Event\LeaveEvent ||
        $event instanceof \LINE\LINEBot\Event\UnfollowEvent) {
        // todo: remove cache
        continue;
    }
    if ($event instanceof \LINE\LINEBot\Event\JoinEvent ||
        $event instanceof \LINE\LINEBot\Event\FollowEvent) {
        $response = $bot->replyMessage(
            $event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('Hi!!')
        );
        continue;
    }

    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {
        $profile = [];
        $line_id = $event->getEventSourceId();
        $response = $bot->getProfile($line_id);
        if ($response->isSucceeded()) {
            $profile = $response->getJSONDecodedBody();
        }

        $parent_ts = null;

        $line_cache_file_path = get_cache_file_path($line_id);
        $line_cache = get_cache($line_cache_file_path);
        if ($line_cache !== null) {
            $parent_ts = $line_cache['ts'];
        }

        $texts = [
            '*LINEメッセージを受信しました*',
            '送信者：' . (isset($profile['displayName']) ? $profile['displayName'] : '') . '（ `' . $line_id . '` ）さん',
            //isset($profile['pictureUrl']) ? $profile['pictureUrl'] : '',
            '',
            '内容：',
            '>' . str_replace("\n", "\n>", $event->getText())
        ];

        $chatPostMessage = new \Slack\ChatPostMessage(
            getenv('SLACK_CHANNEL'),
            getenv('SLACK_BOT_OAUTH_TOKEN')
        );
        $postResult = $chatPostMessage->post(
            new \Slack\MessageBuilder\MessageText(implode("\n", $texts)),
            new \Slack\ChatOptions(['thread_ts' => $parent_ts])
        );
        if ( ! $postResult->isOk()) {
            error_log($postResult->error());
            continue;
        }

        $ts = $postResult->get('ts');

        if ($parent_ts === null) {
            write_cache($line_cache_file_path, ['ts' => $ts]);
        }

        $slack_cache_file_path = get_cache_file_path($ts);
        $slack_cache = get_cache($slack_cache_file_path);
        if ($slack_cache === null) {
            write_cache($slack_cache_file_path, ['line_id' => $line_id]);
        }

        if ($postResult->isOk()) {
            $texts[] = '```';
            $texts[] = 'system informations';
            $texts[] = 'ts:' . $ts;
            $texts[] = '```';
            $chatUpdateMessage = new \Slack\ChatUpdateMessage(
                $postResult->get('channel'),
                getenv('SLACK_BOT_OAUTH_TOKEN')
            );
            $postResult = $chatUpdateMessage->post(
                new \Slack\MessageBuilder\MessageText(implode("\n", $texts)),
                new \Slack\ChatOptions(['ts' => $ts])
            );
            if ( ! $postResult->isOk()) {
                error_log($postResult->error());
            }

            $response = $bot->replyMessage(
                $event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('OK!!')
            );
        }
    } else {
        $response = $bot->replyMessage(
            $event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('???')
        );
    }
}

function get_cache_file_path($file_name)
{
    return __DIR__ . '/../../data/cache/' . $file_name;
}

function get_cache($file_path)
{
    if ( ! file_exists($file_path)) {
        return null;
    }

    $string = file_get_contents($file_path);

    return json_decode($string, true);
}

function write_cache($file_path, $data)
{
    file_put_contents($file_path, json_encode($data));
}