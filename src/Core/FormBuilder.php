<?php

namespace Mystamyst\Tablenice\Core;

use Mystamyst\Tablenice\Forms\Fields\Field;

class FormBuilder
{
    public static function make(array $fields): array
    {
        return collect($fields)
            ->map(fn ($field) => $field instanceof Field ? $field : null) // Ensure only Field instances
            ->filter()
            ->toArray();
    }
}