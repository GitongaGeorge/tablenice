<?php

namespace Mystamyst\Tablenice\Forms\Concerns;

use Mystamyst\Tablenice\Forms\Fields\Field;

trait HasFields
{
    /**
     * An array of Field instances.
     * @return array<Field>
     */
    public function fields(): array
    {
        return [];
    }
}