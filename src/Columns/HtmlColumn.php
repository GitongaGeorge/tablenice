<?php

namespace Mystamyst\Tablenice\Columns;

class HtmlColumn extends Column
{
    // Override render to allow direct HTML output
    public function render(\Illuminate\Database\Eloquent\Model $record)
    {
        $value = parent::getValue($record);
        return new \Illuminate\Support\HtmlString($value);
    }
}