<?php
/**
 * Created by PhpStorm.
 * User: sanokouhei
 * Date: 2019-03-29
 * Time: 10:43
 */

namespace Slack\MessageBuilder\Block;


use Slack\MessageBuilder\MessageBuilderInterface;

class Divider implements MessageBuilderInterface
{

    /**
     * @return array
     */
    public function build()
    {
        return [
            'type' => 'divider'
        ];
    }
}