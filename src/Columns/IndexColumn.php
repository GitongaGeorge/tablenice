<?php

namespace Mystamyst\Tablenice\Columns;

use Illuminate\Database\Eloquent\Model;

class IndexColumn extends Column
{
    public function __construct(string $label = '#')
    {
        parent::__construct('index', $label);
    }

    public static function make(string $name, string|null $label = null): static
    {
        // The $name parameter is ignored for IndexColumn, but required for compatibility.
        return new static($label ?? '#');
    }

    public function getValue(Model $record): mixed
    {
        // This column's value is dependent on the pagination/loop, not the model.
        // It will be handled in the Blade view.
        return null;
    }

    public function render(Model $record)
    {
        // This column will typically be rendered in the Blade loop with the loop index.
        return null; // Will be handled in the Blade.
    }
}