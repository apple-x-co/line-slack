<?php
/**
 * Created by PhpStorm.
 * User: sanokouhei
 * Date: 2019-03-29
 * Time: 10:43
 */

namespace Slack\SlackBlock;


class Divider implements \Slack\SlackBlockInterface
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