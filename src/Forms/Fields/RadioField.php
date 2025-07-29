<?php

namespace Mystamyst\Tablenice\Forms\Fields;

class RadioField extends Field
{
    protected array $options = []; // ['value' => 'label']

    public function options(array $options): static
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}