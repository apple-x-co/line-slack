<?php


namespace Slack\MessageBuilder;


interface MessageBuilderInterface
{
    /**
     * @return array
     */
    public function build();
}