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
     * @return array
     */
    public function build()
    {
        return ['text' => $this->text];
    }
}