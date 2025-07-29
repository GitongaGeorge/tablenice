<?php

namespace Mystamyst\Tablenice\Summaries;

use Illuminate\Database\Eloquent\Builder;

class MaxSummary extends Summary
{
    public function calculate(Builder $query): mixed
    {
        $max = $query->max($this->column);
        return $this->formatValue($max);
    }
}