<?php

namespace Mystamyst\TableNice\Summaries\Contracts;

use Illuminate\Support\Collection;

interface Summary
{
    /**
     * Calculate the summary value.
     *
     * @param Collection $items The collection of items to calculate the summary for.
     * @param string $attribute The attribute/column name to perform the calculation on.
     * @return mixed The calculated summary value.
     */
    public function calculate(Collection $items, string $attribute): mixed;
}
