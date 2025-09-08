<?php

namespace Mystamyst\TableNice\QueryFilters;

use Closure;
use App\DataTables\Columns\Column;

class Filter
{
    public function handle(array $state, Closure $next): array
    {
        $query = $state['query'];
        $activeFilters = $state['active_filters'] ?? [];
        /** @var Column[] $allColumns */
        $allColumns = $state['all_columns'] ?? [];

        if (!empty($activeFilters)) {
            foreach ($activeFilters as $columnName => $value) {
                if (!empty($value)) {
                    $column = collect($allColumns)->first(fn(Column $c) => $c->getName() === $columnName);
                    
                    if ($column) {
                        $column->filterLogic($query, $value);
                    }
                }
            }
        }

        return $next($state);
    }
}
