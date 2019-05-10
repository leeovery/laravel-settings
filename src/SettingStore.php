<?php

namespace Leeovery\LaravelSettings;

use JsonSerializable;

class SettingStore implements JsonSerializable
{
    private $value;

    /**
     * @var null
     */
    private $label;

    /**
     * @var null
     */
    private $validator;

    /**
     * SettingStore constructor.
     *
     * @param $value
     * @param  null  $label
     * @param  null  $validator
     */
    public function __construct($value, $label = null, $validator = null)
    {
        $this->value = $value;
        $this->label = $label;
        $this->validator = $validator;
    }

    public static function make($value, $label = null, $validator = null)
    {
        return new SettingStore($value, $label, $validator);
    }

    /**
     * @param  SettingStore  $settingStore
     * @return bool
     */
    public function compareValues(SettingStore $settingStore): bool
    {
        return $this->getValue() === $settingStore->getValue();
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function set($value)
    {
        $this->value = $value;

        return $this;
    }

    public function toJson()
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return [
            'value' => $this->getValue(),
            'label' => $this->getLabel(),
        ];
    }

    /**
     * @return string|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    public function __toString()
    {
        return (string) $this->getValue();
    }
}