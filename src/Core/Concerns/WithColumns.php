<?php

namespace Mystamyst\Tablenice\Core\Concerns;

use Mystamyst\Tablenice\Columns\Column;

trait WithColumns
{
    /**
     * An array of Column instances.
     *
     * @return array<Column>
     */
    public function getColumns(): array
    {
        return [];
    }
}