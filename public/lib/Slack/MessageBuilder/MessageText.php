<?php


namespace Slack\MessageBuilder;


class MessageText implements MessageBuilderInterface
{
    /** @var string */
    private $text;

    /**
     * SlackText constructor.
     *
     * @param string $text
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function build()
    {
        return $this->text;
    }
}