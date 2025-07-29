<?php

namespace Mystamyst\Tablenice\Summaries;

use Illuminate\Database\Eloquent\Builder;

class MinSummary extends Summary
{
    public function calculate(Builder $query): mixed
    {
        $min = $query->min($this->column);
        return $this->formatValue($min);
    }
}