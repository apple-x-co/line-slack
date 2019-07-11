<?php


namespace Slack\MessageBuilder;


class MessageMultiple implements MessageBuilderInterface
{
    /** @var MessageBuilderInterface[] */
    private $messageBuilders;

    public function __construct()
    {
        $this->messageBuilders = [];
    }

    /**
     * @param MessageBuilderInterface $messageBuilder
     *
     * @return $this
     */
    public function addMessageBuilder($messageBuilder)
    {
        $this->messageBuilders[] = $messageBuilder;

        return $this;
    }

    /**
     * @return mixed
     */
    public function build()
    {
        $messages = [];

        foreach ($this->messageBuilders as $messageBuilder) {
            $messages += $messageBuilder->build();
        }

        return $messages;
    }
}