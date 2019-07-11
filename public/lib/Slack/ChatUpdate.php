<?php


namespace Slack;


use Slack\MessageBuilder\MessageBlock;
use Slack\MessageBuilder\MessageBuilderInterface;
use Slack\MessageBuilder\MessageText;

class ChatUpdate
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
    public function __construct($channel, $token, $as_user = true, $url = 'https://slack.com/api/chat.update')
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

        return new PostResult($result);
    }

    /**
     * @param string $ts
     * @param array|MessageBuilderInterface $data
     *
     * @return PostResult
     */
    public function post($ts, $data)
    {
        if ($data instanceof MessageBlock) {
            $data = [
                'channel' => $this->channel,
                'ts'      => $ts,
                'as_user' => $this->as_user,
                'blocks'  => $data->build()
            ];
        } else if ($data instanceof MessageText) {
            $data = [
                'channel' => $this->channel,
                'ts'      => $ts,
                'text'    => $data->build(),
                'as_user' => $this->as_user
            ];
        }

        return $this->_post($data);
    }
}