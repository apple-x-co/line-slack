<?php

namespace Slack;


class Slack
{
    /** @var string */
    private $url = null;

    /** @var string|null */
    private $channel = null;

    /** @var string|null */
    private $username = null;

    /** @var string|null */
    private $token = null;

    /**
     * Slack constructor.
     *
     * @param string $url
     * @param string|null $channel
     * @param string|null $username
     * @param string|null $token
     */
    public function __construct($url, $channel = null, $username = null, $token = null)
    {
        $this->url      = $url;
        $this->channel  = $channel;
        $this->username = $username;
        $this->token    = $token;
    }

    /**
     * @param array|SlackBlock|SlackText $data
     *
     * @return array
     */
    private function makeData($data)
    {
        if ($data instanceof SlackBlock) {
            $data = [
                'channel' => $this->channel,
                'blocks'  => $data->build()
            ];
        } else if ($data instanceof SlackText) {
            $data = [
                'channel' => $this->channel,
                'text'    => $data->getText()
            ];
        }

        return $data;
    }

    /**
     * @return bool
     */
    private function _send($data)
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

        return $result === 'ok';
    }

    /**
     * @param array|SlackBlock|SlackText $data
     *
     * @return bool
     */
    public function send($data)
    {
        $data = $this->makeData($data);

        return $this->_send($data);
    }

    /**
     * @return bool
     *
     * @see https://api.slack.com/methods/chat.postMessage
     */
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

        return false; // debug
    }

    /**
     * @param array|SlackBlock|SlackText $data
     *
     * @return bool
     */
    public function post($data)
    {
        $data = $this->makeData($data);

        return $this->_post($data);
    }
}