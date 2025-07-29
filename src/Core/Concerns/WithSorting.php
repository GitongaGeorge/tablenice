<?php

namespace Mystamyst\Tablenice\Core\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait WithSorting
{
    public ?string $sortColumn = null;
    public ?string $sortDirection = 'asc'; // 'asc' or 'desc'

    public function sortBy(string $column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    protected function applySorting(Builder $query): Builder
    {
        if ($this->sortColumn) {
            // Find the column definition to get the actual attribute/relationship column for sorting
            $columnInstance = collect($this->getColumns())
                ->first(fn ($column) => $column->getName() === $this->sortColumn);

            if ($columnInstance && $columnInstance->isSortable()) {
                $sortAttribute = $columnInstance->getSortAttribute() ?? $columnInstance->getAttribute();
                if ($sortAttribute) {
                    $query->orderBy($sortAttribute, $this->sortDirection);
                }
            }
        }
        return $query;
    }
}