<?php

namespace Mystamyst\Tablenice\Forms\Fields;

class SelectField extends Field
{
    protected array $options = []; // ['value' => 'label']
    protected bool $multiple = false;

    public function options(array $options): static
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;
        if ($multiple) {
            $this->rules[] = 'array';
        }
        return $this;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }
}