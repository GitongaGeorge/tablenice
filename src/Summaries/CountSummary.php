<?php

namespace Mystamyst\Tablenice\Summaries;

use Illuminate\Database\Eloquent\Builder;

class CountSummary extends Summary
{
    public function calculate(Builder $query): mixed
    {
        $count = $query->count($this->column);
        return $this->formatValue($count);
    }
}