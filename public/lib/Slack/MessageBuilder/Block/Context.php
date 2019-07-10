<?php
/**
 * Created by PhpStorm.
 * User: sanokouhei
 * Date: 2019-03-29
 * Time: 10:43
 */

namespace Slack\MessageBuilder\Block;


use Slack\MessageBuilder\MessageBuilderInterface;

class Context implements MessageBuilderInterface
{
    /** @var ContextElement[] */
    private $elements = [];

    /**
     * @param ContextElement $element
     *
     * @return $this
     */
    public function addElement($element)
    {
        $this->elements[] = $element;

        return $this;
    }

    /**
     * @return array
     */
    public function build()
    {
        $array = [];
        foreach ($this->elements as $element) {
            $array[] = $element->build();
        }

        return [
            'type' => 'context',
            'elements' => $array
        ];
    }
}