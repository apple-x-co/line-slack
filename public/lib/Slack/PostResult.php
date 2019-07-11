<?php


namespace Slack;


class PostResult
{
    /** @var array */
    private $data = [];

    /**
     * PostResult constructor.
     *
     * @param string $text
     */
    public function __construct($text)
    {
        // {
        // 	"ok": true,
        // 	"channel": "CKX1SB24A",
        // 	"ts": "1562663195.002800",
        // 	"message": {
        // 		"type": "message",
        // 		"subtype": "bot_message",
        // 		"text": "Test",
        // 		"ts": "1562663195.002800",
        // 		"username": "line slack",
        // 		"bot_id": "BL8E942LQ"
        // 	}
        // }

        if ($text === 'ok') {
            $this->data = ['ok' => true];
        } else if ($text === 'ng') {
            $this->data = ['ok' => false];
        } else {
            $this->data = @json_decode($text, true);
        }
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return isset($this->data['ok']) && $this->data['ok'];
    }

    /**
     * @return string
     */
    public function error()
    {
        return $this->data['error'];
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
}