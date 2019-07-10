<?php


namespace Slack\MessageBuilder;


interface MessageBuilderInterface
{
    /**
     * @return mixed
     */
    public function build();
}