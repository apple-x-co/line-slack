<?php
/**
 * Created by PhpStorm.
 * User: sanokouhei
 * Date: 2019-03-29
 * Time: 10:42
 */

namespace Slack\MessageBuilder\Block;


use Slack\MessageBuilder\AccessoryInterface;
use Slack\MessageBuilder\MessageBuilderInterface;

class Section implements MessageBuilderInterface
{
    /** @var string */
    private $section_type = null;

    /** @var string */
    private $type = null;

    /** @var string */
    private $text = null;

    /** @var AccessoryInterface */
    private $accessory = null;

    /** @var SectionField[] */
    private $fields = [];

    /**
     * @param $type
     * @param $text
     *
     * @return Section
     */
    public static function text($type, $text)
    {
        $instance = new static();
        $instance->section_type = 'text';
        $instance->type = $type;
        $instance->text = $text;

        return $instance;
    }

    /**
     * @return Section
     */
    public static function fields()
    {
        $instance = new static();
        $instance->section_type = 'fields';

        return $instance;
    }

    /**
     * @param SectionField $field
     *
     * @return $this
     * @throws \Exception
     */
    public function addField($field)
    {
        if ($this->section_type !== 'fields') {
            throw new \Exception('');
        }

        $this->fields[] = $field;

        return $this;
    }

    /**
     * @param AccessoryInterface $accessory
     *
     * @return $this
     */
    public function setAccessory($accessory)
    {
        $this->accessory = $accessory;

        return $this;
    }

    /**
     * @return array
     */
    public function build()
    {
        if ($this->section_type === 'text') {
            $array = [
                'type' => 'section',
                'text' => [
                    'type' => $this->type,
                    'text' => $this->text
                ]
            ];

            if ($this->accessory !== null) {
                $array = array_merge($array, ['accessory' => $this->accessory->build()]);
            }

            return $array;
        }

        if ($this->section_type === 'fields') {
            $array = [];
            foreach ($this->fields as $field) {
                $array[] = $field->build();
            }

            return [
                'type' => 'section',
                'fields' => $array
            ];
        }
    }
}