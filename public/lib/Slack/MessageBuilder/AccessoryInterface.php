<?php


namespace Slack\MessageBuilder;


interface AccessoryInterface
{
    /**
     * @return array
     */
    public function build();
}