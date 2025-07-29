<?php

namespace Mystamyst\Tablenice\Summaries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class VarianceSummary extends Summary
{
    public function calculate(Builder $query): mixed
    {
        // SQL function for variance might vary by database.
        // For MySQL: VAR_POP or VAR_SAMP
        // For PostgreSQL: VAR_POP or VAR_SAMP
        $variance = $query->select(DB::raw("VAR_POP({$this->column}) as variance"))->value('variance');
        return $this->formatValue($variance);
    }
}