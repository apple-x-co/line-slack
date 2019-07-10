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
        $profile = [];
        $line_id = $event->getEventSourceId();
        $response = $bot->getProfile($line_id);
        if ($response->isSucceeded()) {
            $profile = $response->getJSONDecodedBody();
        }

        $parent_ts = null;

        $cache_file_path = get_cache_file_path($line_id);
        $cache = get_cache($cache_file_path);
        if ($cache !== null) {
            $parent_ts = $cache['ts'];
        }

        $texts = [
            'LINEより ' . (isset($profile['displayName']) ? $profile['displayName'] : '') . '（ `' . $line_id . '` ）さんからの問い合わせです。',
            //isset($profile['pictureUrl']) ? $profile['pictureUrl'] : '',
            '',
            $event->getText(),
            '',
        ];

        $chatPostMessage = new \Slack\ChatPostMessage(
            getenv('SLACK_CHANNEL'),
            getenv('SLACK_BOT_OAUTH_TOKEN')
        );
        $postResult = $chatPostMessage->post(
            new \Slack\MessageBuilder\MessageText(implode("\n", $texts)),
            isset($cache['ts']) ? $cache['ts'] : null
        );
        if ( ! $postResult->isOk()) {
            error_log($postResult->error());
            continue;
        }

        if ($cache === null) {
            write_cache($cache_file_path, ['ts' => $postResult->get('ts')]);
        }

        if ($postResult->isOk()) {
            $texts[] = '```';
            $texts[] = 'system informations';
            $texts[] = 'ts:' . $postResult->get('ts');
            $texts[] = '```';
            $chatUpdate = new \Slack\ChatUpdate(
                $postResult->get('channel'),
                getenv('SLACK_BOT_OAUTH_TOKEN')
            );
            $postResult = $chatUpdate->post(
                $postResult->get('ts'),
                new \Slack\MessageBuilder\MessageText(implode("\n", $texts))
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

function get_cache_file_path($line_id)
{
    return __DIR__ . '/../../data/cache/' . $line_id;
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