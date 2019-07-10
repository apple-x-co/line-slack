<?php


namespace Slack;


class PostResult
{
    /** @var array */
    private $data;

    /**
     * PostResult constructor.
     *
     * @param string $text
     */
    public function __construct($text)
    {
        $this->data = @json_decode($text, true);
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