<?php

namespace Mystamyst\Tablenice\Forms\Fields;

class DateTimeField extends Field
{
    protected ?string $format = null; // Display format, e.g., 'Y-m-d H:i:s'

    public function __construct(string $name, ?string $label = null)
    {
        parent::__construct($name, $label);
        $this->rules('date');
        $this->type('datetime-local'); // Set input type
    }

    public function format(string $format): static
    {
        $this->format = $format;
        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }
}