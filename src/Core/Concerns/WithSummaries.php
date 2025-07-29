<?php

namespace Mystamyst\Tablenice\Core\Concerns;

use Mystamyst\Tablenice\Summaries\Summary;

trait WithSummaries
{
    /**
     * An array of Summary instances.
     *
     * @return array<Summary>
     */
    public function getSummaries(): array
    {
        return [];
    }

    /**
     * Calculate the summary values.
     *
     * @return array
     */
    public function calculateSummaries(): array
    {
        $summaries = [];
        $baseQuery = $this->applyConcerns($this->query()); // Get query before pagination

        foreach ($this->getSummaries() as $summary) {
            // Clone the query for each summary to avoid interference
            $summaries[$summary->getName()] = $summary->calculate(clone $baseQuery);
        }

        return $summaries;
    }
}