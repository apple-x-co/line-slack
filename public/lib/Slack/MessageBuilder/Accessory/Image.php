<?php


namespace Slack\MessageBuilder\Accessory;


use Slack\MessageBuilder\AccessoryInterface;

class Image implements AccessoryInterface
{
    /** @var string */
    private $url;

    /** @var string */
    private $alt;

    /**
     * Image constructor.
     *
     * @param string $url
     * @param string $alt
     */
    public function __construct($url, $alt)
    {
        $this->url = $url;
        $this->alt = $alt;
    }

    /**
     * @return array
     */
    public function build()
    {
        return [
            'type'      => 'image',
            'image_url' => $this->url,
            'alt_text'  => $this->alt
        ];
    }
}