<?php

namespace Mystamyst\Tablenice\Forms\Fields;

class TextInput extends Field
{
    protected string $type = 'text';
    protected ?string $placeholder = null;

    public function type(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }
}