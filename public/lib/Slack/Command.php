<?php


namespace Slack;


class Command
{
    /** @var string[] */
    private $texts;

    /**
     * CommandParser constructor.
     *
     * @param $text
     */
    public function __construct($text)
    {
        $this->texts = explode(' ', $text);
    }

    /**
     * @return string|null
     */
    public function getAction()
    {
        if (empty($this->texts)) {
            return null;
        }

        return $this->texts[0];
    }

    /**
     * @return array|null
     */
    public function getArguments()
    {
        if (empty($this->texts)) {
            return [];
        }

        return array_slice($this->texts, 1);
    }

}