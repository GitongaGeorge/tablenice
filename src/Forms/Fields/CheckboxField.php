<?php

namespace Mystamyst\Tablenice\Forms\Fields;

class CheckboxField extends Field
{
    public function __construct(string $name, ?string $label = null)
    {
        parent::__construct($name, $label);
        $this->rules('boolean'); // Checkbox values are typically boolean
    }
}