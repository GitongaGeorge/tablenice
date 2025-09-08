<?php

namespace Mystamyst\TableNice\QueryFilters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use App\DataTables\Columns\Column;

class Search
{
    public function handle(array $state, Closure $next): array
    {
        $query = $state['query'];
        $searchTerm = $state['search_term'] ?? null;
        /** @var Column[] $searchableColumns */
        $searchableColumns = $state['searchable_columns'] ?? [];

        if (!empty($searchTerm) && !empty($searchableColumns)) {
            $query->where(function (Builder $q) use ($searchTerm, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $column->searchLogic($q, $searchTerm);
                }
            });
        }
        
        return $next($state);
    }
}
