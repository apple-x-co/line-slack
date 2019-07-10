<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::create(__DIR__ . '/../');
$dotenv->load();

$body = file_get_contents('php://input');

$result = new \Slack\EventCallbackResult($body);

if ($result->getToken() !== getenv('SLACK_VERIFICATION_TOKEN')) {
    error_log('token error');
    error_log($result->getToken());
    exit(1);
}

if ($result->getEventType() === 'app_mention') {
    $thread_ts = $result->getEventThreadTS();
    if ($thread_ts === null || $thread_ts === '') {
        $chatPostMessage = new \Slack\ChatPostMessage(
            getenv('SLACK_CHANNEL'),
            getenv('SLACK_BOT_OAUTH_TOKEN')
        );
        $postResult = $chatPostMessage->post(
            new \Slack\MessageBuilder\MessageText('Sorry!! not thread.'),
            $result->getEventTS()
        );
        if ( ! $postResult->isOk()) {
            error_log($postResult->error());
        }
        return;
    }

    $slack_cache_file_path = get_cache_file_path($thread_ts);
    $slack_cache = get_cache($slack_cache_file_path);
    if ($slack_cache === null) {
        $chatPostMessage = new \Slack\ChatPostMessage(
            getenv('SLACK_CHANNEL'),
            getenv('SLACK_BOT_OAUTH_TOKEN')
        );
        $postResult = $chatPostMessage->post(
            new \Slack\MessageBuilder\MessageText('Sorry!! Cannot find line user.'),
            $thread_ts
        );
        if ( ! $postResult->isOk()) {
            error_log($postResult->error());
        }
        return;
    }

    $reply_text = $result->getEventTextWithoutMention();
    $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('LINE_ACCESS_TOKEN'));
    $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('LINE_CHANNEL_SECRET')]);

    $bot->pushMessage(
        $slack_cache['line_id'],
        new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($reply_text)
    );

} else {
    error_log($body);
}

// mention message
//{
//  "token": "vDhLPAaHITeq1GFnRZeJHoPz",
//	"team_id": "T0454LWHB",
//	"api_app_id": "ALAHMAZ1C",
//	"event": {
//    "client_msg_id": "8eca2e08-355b-4672-a506-02bf2596b3f3",
//		"type": "app_mention",
//		"text": "<@UKX2KDL91> hello",
//		"user": "U3Q2NNAUF",
//		"ts": "1562657462.002000",
//		"team": "T0454LWHB",
//		"channel": "CKX1SB24A",
//		"event_ts": "1562657462.002000"
//	},
//	"type": "event_callback",
//	"event_id": "EvLAARKPQF",
//	"event_time": 1562657462,
//	"authed_users": [
//    "UKX2KDL91"
//]
//}

// mention message in thread （parent_user_idがつく）
//{
//  "token": "vDhLPAaHITeq1GFnRZeJHoPz",
//	"team_id": "T0454LWHB",
//	"api_app_id": "ALAHMAZ1C",
//	"event": {
//    "client_msg_id": "aa4a7562-a5ed-44a4-9e7f-d3223714d9ff",
//		"type": "app_mention",
//		"text": "<@UKX2KDL91> hello2",
//		"user": "U3Q2NNAUF",
//		"ts": "1562658521.002100",
//		"team": "T0454LWHB",
//		"thread_ts": "1562657454.001800",
//		"parent_user_id": "U3Q2NNAUF",
//		"channel": "CKX1SB24A",
//		"event_ts": "1562658521.002100"
//	},
//	"type": "event_callback",
//	"event_id": "EvL25ATEKT",
//	"event_time": 1562658521,
//	"authed_users": [
//    "UKX2KDL91"
//]
//}

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