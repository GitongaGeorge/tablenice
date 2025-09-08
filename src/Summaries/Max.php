<?php

namespace Mystamyst\TableNice\Summaries;

use Mystamyst\TableNice\Summaries\Contracts\Summary;
use Illuminate\Support\Collection;

class Max implements Summary
{
    public function calculate(Collection $items, string $attribute): mixed
    {
        return $items->max($attribute);
    }
}
