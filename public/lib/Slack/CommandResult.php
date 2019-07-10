<?php


namespace Slack;


class CommandResult
{
    private $data;

    /**
     * CommandResult constructor.
     *
     * @param string $text
     */
    public function __construct($text)
    {
        //token=vDhLPAaHITeq1GFnRZeJHoPz&
        //team_id=T0454LWHB&
        //team_domain=buddying&
        //channel_id=CKX1SB24A&
        //channel_name=line-slack&
        //user_id=U3Q2NNAUF&
        //user_name=buddying.sano&
        //command=%2Flineslack&
        //text=argv1+argv2&
        //response_url=https%3A%2F%2Fhooks.slack.com%2Fcommands%2FT0454LWHB%2F683441892913%2FeENyZLIKiU8NOARTaQSIwrBF&
        //trigger_id=689320993604.4174710589.5a07bcca99e1c28f63a273d30737e482

        $result = null;
        @parse_str($text, $result);
        $this->data = $result;
    }

    /**
     * @return string|null
     */
    public function getToken()
    {
        return isset($this->data['token']) ? $this->data['token'] : null;
    }

    /**
     * @return string|null
     */
    public function getUserName()
    {
        return isset($this->data['user_name']) ? $this->data['user_name'] : null;
    }

    /**
     * @return string|null
     */
    public function getCommand()
    {
        return isset($this->data['command']) ? $this->data['command'] : null;
    }

    /**
     * @return string|null
     */
    public function getText()
    {
        return isset($this->data['text']) ? $this->data['text'] : null;
    }

    /**
     * @return string|null
     */
    public function getResponseUrl()
    {
        return isset($this->data['response_url']) ? $this->data['response_url'] : null;
    }
}