<?php
/**
 * Created by PhpStorm.
 * User: sanokouhei
 * Date: 2019-03-29
 * Time: 10:45
 */

namespace Slack\SlackBlock;


class SectionField implements \Slack\SlackBlockInterface
{
    /** @var string */
    private $type;

    /** @var string */
    private $text;

    /**
     * SectionField constructor.
     *
     * @param string $type
     * @param string $text
     */
    public function __construct($type, $text)
    {
        $this->type = $type;
        $this->text = $text;
    }

    /**
     * @return array
     */
    public function build()
    {
        return [
            'type' => $this->type,
            'text' => $this->text
        ];
    }
}