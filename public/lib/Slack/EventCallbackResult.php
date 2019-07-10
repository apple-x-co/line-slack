<?php


namespace Slack;


class EventCallbackResult
{
    /** @var array */
    public $data;

    /**
     * Event constructor.
     *
     * @param string $text
     */
    public function __construct($text)
    {
        $this->data = @json_decode($text, true);
    }

    /**
     * @return array|null
     */
    private function getEvent()
    {
        return isset($this->data['event']) ? $this->data['event'] : null;
    }

    /**
     * @return string|null
     *
     * ex: app_mention
     */
    public function getEventType()
    {
        $event = $this->getEvent();
        if ($event === null) {
            return null;
        }

        return isset($event['type']) ? $event['type'] : null;
    }

    /**
     * @return string|null
     *
     * ex: app_mention
     */
    public function getEventChannel()
    {
        $event = $this->getEvent();
        if ($event === null) {
            return null;
        }

        return isset($event['channel']) ? $event['channel'] : null;
    }

    /**
     * @return string|null
     *
     * ex: 1562728945.004300
     */
    public function getEventTS()
    {
        $event = $this->getEvent();
        if ($event === null) {
            return null;
        }

        return isset($event['ts']) ? $event['ts'] : null;
    }

    /**
     * @return string|null
     *
     * ex: 1562728945.004300
     */
    public function getEventThreadTS()
    {
        $event = $this->getEvent();
        if ($event === null) {
            return null;
        }

        return isset($event['thread_ts']) ? $event['thread_ts'] : null;
    }

    /**
     * @return string|null
     *
     * ex: <@UKX2KDL91> TEST Reply
     */
    public function getEventText()
    {
        $event = $this->getEvent();
        if ($event === null) {
            return null;
        }

        return isset($event['text']) ? $event['text'] : null;
    }

    /**
     * @return string|null
     *
     * ex: TEST Reply
     */
    public function getEventTextWithoutMention()
    {
        $text = $this->getEventText();
        if ($text === null) {
            return null;
        }

        $match = null;
        preg_match('/\A\<\@\w+?\> (.+)/', $text, $match);
        if (is_array($match)) {
            return $match[1];
        }

        return null;
    }

}