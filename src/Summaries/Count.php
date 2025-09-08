<?php

namespace Mystamyst\TableNice\Summaries;

use Mystamyst\TableNice\Summaries\Contracts\Summary;
use Illuminate\Support\Collection;

class Count implements Summary
{
    public function calculate(Collection $items, string $attribute): mixed
    {
        // Count just counts the number of items in the collection.
        // The attribute is not needed but is part of the contract.
        return $items->count();
    }
}
