<?php


namespace Slack;


use Slack\MessageBuilder\MessageBuilderInterface;

class ChatUpdateMessage extends AbstractChatMessage
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
     * @param array|MessageBuilderInterface $data
     * @param ChatOptions $options
     *
     * @return PostResult
     */
    public function post($data, $options)
    {
        if ($data instanceof MessageBuilderInterface) {
            $array = [
                'channel' => $this->channel,
                'as_user' => $this->as_user,
                'ts'      => $options->get('ts'),
            ];
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