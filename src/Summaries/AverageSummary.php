<?php

namespace Mystamyst\Tablenice\Summaries;

use Illuminate\Database\Eloquent\Builder;

class AverageSummary extends Summary
{
    public function calculate(Builder $query): mixed
    {
        $average = $query->avg($this->column);
        return $this->formatValue($average);
    }
}