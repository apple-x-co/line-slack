<?php


namespace Slack;


class SlackText
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
    public function getText()
    {
        return $this->text;
    }
}