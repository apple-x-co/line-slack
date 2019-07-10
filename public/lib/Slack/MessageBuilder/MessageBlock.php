<?php

namespace Slack\MessageBuilder;


use Slack\MessageBuilder\Block\BlockInterface;

class MessageBlock implements BlockInterface, MessageBuilderInterface
{
    private $blocks = [];

    /**
     * @param BlockInterface $block
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