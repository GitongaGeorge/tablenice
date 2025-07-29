<?php

namespace Mystamyst\Tablenice\Forms\Fields;

class TextareaField extends Field
{
    protected int $rows = 3;

    public function rows(int $rows): static
    {
        $this->rows = $rows;
        return $this;
    }

    public function getRows(): int
    {
        return $this->rows;
    }
}