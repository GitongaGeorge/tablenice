<?php

namespace Mystamyst\TableNice\Forms;

use Mystamyst\TableNice\Forms\Fields\Field;

abstract class Form
{
    /**
     * Define the fields that make up the form.
     *
     * @return Field[]
     */
    abstract public function getFields(): array;

    /**
     * Get the form fields as an associative array.
     *
     * @return array
     */
    public function schema(): array
    {
        return collect($this->getFields())
            ->mapWithKeys(fn (Field $field) => [$field->getName() => $field])
            ->all();
    }
}
