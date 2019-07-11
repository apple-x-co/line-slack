<?php

namespace Slack\MessageBuilder;


class MessageBlock implements  MessageBuilderInterface
{
    private $blocks = [];

    /**
     * @param MessageBuilderInterface $block
     *
     * @return $this
     */
    public function addBlock($block)
    {
        $this->blocks[] = $block;

        return $this;
    }

    /**
     * @return array
     */
    public function build()
    {
        $array = [];
        foreach ($this->blocks as $block) {
            $array[] = $block->build();
        }

        return $array;
    }
}