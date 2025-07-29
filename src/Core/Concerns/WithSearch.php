<?php

namespace Mystamyst\Tablenice\Core\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait WithSearch
{
    public ?string $search = '';

    protected function applySearch(Builder $query): Builder
    {
        if ($this->search) {
            $searchableColumns = collect($this->getColumns())
                ->filter(fn ($column) => $column->isSearchable())
                ->map(fn ($column) => $column->getAttribute())
                ->filter()
                ->toArray();

            if (!empty($searchableColumns)) {
                $query->where(function ($q) use ($searchableColumns) {
                    foreach ($searchableColumns as $column) {
                        $q->orWhere($column, 'like', '%' . $this->search . '%');
                    }
                });
            }
        }
        return $query;
    }
}