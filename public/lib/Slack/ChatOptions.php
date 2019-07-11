<?php


namespace Slack;


class ChatOptions
{
    /** @var array */
    private $options;

    public function __construct($options = [])
    {
        $this->options = $options;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }
}