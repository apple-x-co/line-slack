<?php


namespace Slack;


use Slack\MessageBuilder\MessageBuilderInterface;

class ChatPostMessage extends AbstractChatMessage
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

//        // 「ts」がメッセージID
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

    /**
     * @param array|MessageBuilderInterface $data
     * @param ChatOptions $options
     *
     * @return PostResult
     */
    public function post($data, $options)
    {
        if ($data instanceof MessageBuilderInterface) {
            $array = [
                'channel'   => $this->channel,
                'as_user'   => $this->as_user
            ];
            $array = array_merge($array, $options->getArray());
            return $this->_post(
                $this->url,
                array_merge($array, $data->build()),
                $this->token
            );
        }

        return $this->_post(
            $this->url,
            $data,
            $this->token
        );
    }
}