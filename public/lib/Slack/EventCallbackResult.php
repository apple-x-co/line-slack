<?php


namespace Slack;


class EventCallbackResult
{
    /** @var array */
    public $data;

    /**
     * EventCallbackResult constructor.
     *
     * @param string $text
     */
    public function __construct($text)
    {
        // {
        // 	"token": "vDhLPAaHITeq1GFnRZeJHoPz",
        // 	"team_id": "T0454LWHB",
        // 	"api_app_id": "ALAHMAZ1C",
        // 	"event": {
        // 		"client_msg_id": "67382bcd-ac02-4c40-b08d-6d0499c252e4",
        // 		"type": "app_mention",
        // 		"text": "<@UKX2KDL91> TEST Reply",
        // 		"user": "U3Q2NNAUF",
        // 		"ts": "1562728945.004300",
        // 		"team": "T0454LWHB",
        // 		"thread_ts": "1562728864.003600",
        // 		"parent_user_id": "UKX2KDL91",
        // 		"channel": "CKX1SB24A",
        // 		"event_ts": "1562728945.004300"
        // 	},
        // 	"type": "event_callback",
        // 	"event_id": "EvL39RS20H",
        // 	"event_time": 1562728945,
        // 	"authed_users": [
        // 		"UKX2KDL91"
        // 	]
        // }

        $this->data = @json_decode($text, true);
    }

    /**
     * @return string|null
     */
    public function getToken()
    {
        return isset($this->data['token']) ? $this->data['token'] : null;
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
        preg_match('/\A\<\@\w+?\> ([\s\S]*)/', $text, $match);
        if (is_array($match)) {
            return $match[1];
        }

        return null;
    }

}