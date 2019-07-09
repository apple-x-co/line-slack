<?php

namespace Slack;


class Slack
{
    /** @var string */
    private $url = null;

    /** @var string */
    private $channel = null;

    /** @var string */
    private $username = null;

    /**
     * Slack constructor.
     *
     * @param string $url
     * @param string|null $channel
     * @param string|null $username
     */
    public function __construct($url, $channel = null, $username = null)
    {
        $this->url      = $url;
        $this->channel  = $channel;
        $this->username = $username;
    }

    /**
     * @return bool
     */
    private function _send($data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
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
     * @param array|SlackBlock $data
     *
     * @return bool
     */
    public function send($data)
    {
        if ($data instanceof SlackBlock) {
            $data = [
                'channel'    => $this->channel,
                'username'   => $this->username,
                'icon_emoji' => ':rocket:',
                'blocks'     => $data->build()
            ];
        }

        return $this->_send($data);
    }
}