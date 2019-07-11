<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::create(__DIR__ . '/../');
$dotenv->load();

$body = file_get_contents('php://input');

$result = new \Slack\CommandResult($body);

if ($result->getToken() !== getenv('SLACK_VERIFICATION_TOKEN')) {
    error_log('token error');
    error_log($result->getToken());
    exit(1);
}

$command = new \Slack\Command($result->getText());

$action_map = [
    'statistics' => 'getLineStatistics',
    'profile'    => 'getLineProfile',
    'message'    => 'sendLineMessage'
];

if ( ! array_key_exists($command->getAction(), $action_map)) {
    echo 'command refused.';
    exit(1);
}

$function = $action_map[$command->getAction()];
if ( ! function_exists($function)) {
    echo 'unknown command action.';
    exit(1);
}

echo $function($result, $command->getArguments());

/**
 * @param \Slack\CommandResult $result
 * @param array $argv
 */
function getLineStatistics($result, $argv)
{
    $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('LINE_ACCESS_TOKEN'));
    $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('LINE_CHANNEL_SECRET')]);

    $date = new \DateTime();

    $numberOfSentThisMonth = $bot->getNumberOfSentThisMonth()->getJSONDecodedBody();
    $numberOfLimitForAdditional = $bot->getNumberOfLimitForAdditional()->getJSONDecodedBody();
    $numberOfSentBroadcastMessages = $bot->getNumberOfSentBroadcastMessages($date)->getJSONDecodedBody();
    $numberOfSentMulticastMessages = $bot->getNumberOfSentMulticastMessages($date)->getJSONDecodedBody();
    $numberOfSentPushMessages = $bot->getNumberOfSentPushMessages($date)->getJSONDecodedBody();
    $numberOfSentReplyMessages = $bot->getNumberOfSentReplyMessages($date)->getJSONDecodedBody();

    $texts = [
        'LINE Statistics',
        '',
        '*Get number of messages sent this month*',
        'totalUsage: `' . $numberOfSentThisMonth['totalUsage'] . '`',
        '',
        '*Get the target limit for additional messages*',
        'type: `' . $numberOfLimitForAdditional['type'] . '`',
        'value: `' . (isset($numberOfLimitForAdditional['value']) ? $numberOfLimitForAdditional['value'] : '') . '`',
        '',
        '*Get number of sent broadcast messages*',
        'status: `' . $numberOfSentBroadcastMessages['status'] . '`',
        'success: `' . (isset($numberOfSentBroadcastMessages['success']) ? $numberOfSentBroadcastMessages['success'] : '-') . '`',
        '',
        '*Get number of sent multicast messages*',
        'status: `' . $numberOfSentMulticastMessages['status'] . '`',
        'success: `' . (isset($numberOfSentMulticastMessages['success']) ? $numberOfSentMulticastMessages['success'] : '-') . '`',
        '',
        '*Get number of sent push messages*',
        'status: `' . $numberOfSentPushMessages['status'] . '`',
        'success: `' . (isset($numberOfSentPushMessages['success']) ? $numberOfSentPushMessages['success'] : '-') . '`',
        '',
        '*Get number of sent reply messages*',
        'status: `' . $numberOfSentReplyMessages['status'] . '`',
        'success: `' . (isset($numberOfSentReplyMessages['success']) ? $numberOfSentReplyMessages['success'] : '-') . '`',
    ];

    $chatPostMessage = new \Slack\ChatPostMessage(
        getenv('SLACK_CHANNEL'),
        getenv('SLACK_BOT_OAUTH_TOKEN'),
        true,
        $result->getResponseUrl()
    );
    $postResult = $chatPostMessage->post(
        new \Slack\MessageBuilder\MessageText(implode("\n", $texts)),
        new \Slack\ChatOptions()
    );

    if ( ! $postResult->isOk()) {
        error_log($postResult->error());
        return "Slackにメッセージを投稿中にエラーが発生しました。\n" . $postResult->error();
    }

    return 'command accepted.';
}

/**
 * @param \Slack\CommandResult $result
 * @param array $argv
 */
function getLineProfile($result, $argv)
{
    $line_id = $argv[0];

    $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('LINE_ACCESS_TOKEN'));
    $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('LINE_CHANNEL_SECRET')]);
    $response = $bot->getProfile($line_id);
    if ( ! $response->isSucceeded()) {
        error_log($response->getRawBody());
        return "LINEプロフィール取得中にエラーが発生しました。\n" . $response->getRawBody();
    }

    $profile = $response->getJSONDecodedBody();

    $messageBuilder = new \Slack\MessageBuilder\MessageMultiple();
    $messageBuilder->addMessageBuilder(
        new \Slack\MessageBuilder\MessageText('displayName: `' . $profile['displayName'] . '`')
    );

    $chatPostMessage = new \Slack\ChatPostMessage(
        getenv('SLACK_CHANNEL'),
        getenv('SLACK_BOT_OAUTH_TOKEN'),
        true,
        $result->getResponseUrl()
    );
    $postResult = $chatPostMessage->post(
        (new \Slack\MessageBuilder\MessageMultiple())
            ->addMessageBuilder(
                new \Slack\MessageBuilder\MessageText('displayName: `' . $profile['displayName'] . '`')
            )
            ->addMessageBuilder(
                (new \Slack\MessageBuilder\MessageBlock())
                    ->addBlock(
                        (\Slack\MessageBuilder\Block\Section::text('mrkdwn', implode("\n", [
                            '*LINE Profile*',
                            'id: `' . $line_id . '`',
                            'name: `' . $profile['displayName'] . '`',
                        ])))
                            ->setAccessory(
                                new \Slack\MessageBuilder\Accessory\Image($profile['pictureUrl'], $line_id)
                            )
                    )
            ),
        new \Slack\ChatOptions()
    );

    if ( ! $postResult->isOk()) {
        error_log($postResult->error());
        return "Slackにメッセージを投稿中にエラーが発生しました。\n" . $postResult->error();
    }

    return 'command accepted.';
}

/**
 * @param \Slack\CommandResult $result
 * @param array $argv
 */
function sendLineMessage($result, $argv)
{
    list($line_id, $message) = $argv;

    $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('LINE_ACCESS_TOKEN'));
    $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('LINE_CHANNEL_SECRET')]);

    $response = $bot->pushMessage(
        $line_id,
        new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message)
    );
    if ( ! $response->isSucceeded()) {
        error_log($response->getRawBody());
        return "LINEメッセージ送信中にエラーが発生しました。\n" . $response->getRawBody();
    }

    $response = $bot->getProfile($line_id);
    if ( ! $response->isSucceeded()) {
        error_log($response->getRawBody());
        return "LINEプロフィール取得中にエラーが発生しました。\n" . $response->getRawBody();
    }
    $profile = $response->getJSONDecodedBody();
    $texts = [
        '*LINEメッセージを送信しました*',
        '送信者：' . $result->getUserName(),
        '宛先：' . (isset($profile['displayName']) ? $profile['displayName'] : '') . '（ `' . $line_id . '` ）さん',
        '',
        '内容：',
        '>' . str_replace("\n", "\n>", $message)
    ];

    $chatPostMessage = new \Slack\ChatPostMessage(
        getenv('SLACK_CHANNEL'),
        getenv('SLACK_BOT_OAUTH_TOKEN')
    );
    $postResult = $chatPostMessage->post(
        new \Slack\MessageBuilder\MessageText(implode("\n", $texts)),
        new \Slack\ChatOptions()
    );

    $line_cache_file_path = get_cache_file_path($line_id);
    $line_cache = get_cache($line_cache_file_path);
    if ($line_cache === null) {
        $ts = $postResult->get('ts');
        write_cache($line_cache_file_path, ['ts' => $ts]);
    }

    $slack_cache_file_path = get_cache_file_path($postResult->get('ts'));
    $slack_cache = get_cache($slack_cache_file_path);
    if ($slack_cache === null) {
        write_cache($slack_cache_file_path, ['line_id' => $line_id]);
    }

    return 'command accepted.';
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