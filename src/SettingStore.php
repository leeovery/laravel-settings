<?php

namespace Leeovery\LaravelSettings;

use JsonSerializable;

class SettingStore implements JsonSerializable
{
    private $value;

    private ?string $label;

    /**
     * @var null
     */
    private $validator;

    public function __construct($value, $label = null, $validator = null)
    {
        $this->value = $value;
        $this->label = $label;
        $this->validator = $validator;
    }

    public static function make($value, $label = null, $validator = null): SettingStore
    {
        return new SettingStore($value, $label, $validator);
    }

    public function compareValues(SettingStore $settingStore): bool
    {
        return $this->getValue() === $settingStore->getValue();
    }

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

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'value' => $this->getValue(),
            'label' => $this->getLabel(),
        ];
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function __toString()
    {
        return (string) $this->getValue();
    }
}
