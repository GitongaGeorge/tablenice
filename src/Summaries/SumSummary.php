<?php

namespace Mystamyst\Tablenice\Summaries;

use Illuminate\Database\Eloquent\Builder;

class SumSummary extends Summary
{
    public function calculate(Builder $query): mixed
    {
        $sum = $query->sum($this->column);
        return $this->formatValue($sum);
    }
}