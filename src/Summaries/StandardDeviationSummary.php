<?php

namespace Mystamyst\Tablenice\Summaries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StandardDeviationSummary extends Summary
{
    public function calculate(Builder $query): mixed
    {
        // SQL function for standard deviation might vary by database.
        // For MySQL: STDDEV_POP or STDDEV_SAMP
        // For PostgreSQL: STDDEV_POP or STDDEV_SAMP
        // This is a basic example, you might need to adjust for different DBs.
        $stdDev = $query->select(DB::raw("STDDEV_POP({$this->column}) as std_dev"))->value('std_dev');
        return $this->formatValue($stdDev);
    }
}