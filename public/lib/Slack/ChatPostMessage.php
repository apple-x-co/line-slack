<?php


namespace Slack;


use Slack\MessageBuilder\MessageBlock;
use Slack\MessageBuilder\MessageText;

class ChatPostMessage
{
    /** @var string */
    private $url = null;

    /** @var string|null */
    private $channel = null;

    /** @var string|null */
    private $token = null;

    /** @var boolean */
    private $as_user = true;

    /**
     * Slack constructor.
     *
     * @param string $channel
     * @param string $token
     * @param boolean $as_user
     * @param string $url
     */
    public function __construct($channel, $token, $as_user = true, $url = 'https://slack.com/api/chat.postMessage')
    {
        $this->channel = $channel;
        $this->token   = $token;
        $this->as_user = $as_user;
        $this->url     = $url;
    }

    /**
     * @return PostResult
     *
     * @see https://api.slack.com/methods/chat.postMessage
     */
    private function _post($data)
    {
        $headers = ['Content-Type: application/json; charset=utf-8'];
        if ($this->token !== null) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        // 「ts」がメッセージID
//    {
//        "ok": true,
//        "channel": "CKX1SB24A",
//        "ts": "1562663195.002800",
//        "message": {
//            "type": "message",
//            "subtype": "bot_message",
//            "text": "Test",
//            "ts": "1562663195.002800",
//            "username": "line slack",
//            "bot_id": "BL8E942LQ"
//        }
//    }
        return new PostResult($result);
    }

    /**
     * @param array|MessageBlock|MessageText $data
     * @param string $thread_ts
     *
     * @return PostResult
     */
    public function post($data, $thread_ts = null)
    {
        if ($data instanceof MessageBlock) {
            $data = [
                'channel'   => $this->channel,
                'as_user'   => $this->as_user,
                'blocks'    => $data->build(),
                'thread_ts' => $thread_ts
            ];
        } else if ($data instanceof MessageText) {
            $data = [
                'channel'   => $this->channel,
                'text'      => $data->build(),
                'as_user'   => $this->as_user,
                'thread_ts' => $thread_ts
            ];
        }

        return $this->_post($data);
    }
}