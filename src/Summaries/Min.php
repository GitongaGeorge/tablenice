<?php

namespace Mystamyst\TableNice\Summaries;

use App\DataTables\Summaries\Contracts\Summary;
use Illuminate\Support\Collection;

class Min implements Summary
{
    public function calculate(Collection $items, string $attribute): mixed
    {
        return $items->min($attribute);
    }
}
