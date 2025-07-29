<?php

namespace Mystamyst\Tablenice\Core\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait WithFilters
{
    public array $filters = [];

    protected function applyFilters(Builder $query): Builder
    {
        foreach ($this->filters as $key => $value) {
            if (!empty($value) || $value === 0 || $value === false) { // Handle 0 and false as valid filter values
                // This is a basic example. You'd need a more robust filter system
                // (e.g., filter classes, or a mapping from key to query scope).
                // For now, assuming direct column filtering.
                if (method_exists($this, 'applyCustomFilter')) {
                    $query = $this->applyCustomFilter($query, $key, $value);
                } else {
                    $query->where($key, $value);
                }
            }
        }
        return $query;
    }

    // You can override this in your specific datatable to define custom filter logic
    // public function applyCustomFilter(Builder $query, string $key, $value): Builder { ... }
}