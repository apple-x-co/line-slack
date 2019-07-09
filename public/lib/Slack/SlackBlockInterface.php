<?php

namespace Slack;


interface SlackBlockInterface
{
    /**
     * @return array
     */
    public function build();
}