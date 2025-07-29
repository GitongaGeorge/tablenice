<?php

namespace Mystamyst\Tablenice\Core\Concerns;

trait WithStickyColumns
{
    /**
     * The number of columns to make sticky from the left.
     * @var int
     */
    public int $stickyLeftColumns = 0;

    /**
     * The number of columns to make sticky from the right.
     * @var int
     */
    public int $stickyRightColumns = 0;

    public function getStickyLeftColumns(): int
    {
        return $this->stickyLeftColumns;
    }

    public function getStickyRightColumns(): int
    {
        return $this->stickyRightColumns;
    }

    public function setStickyColumns(int $left = 0, int $right = 0)
    {
        $this->stickyLeftColumns = $left;
        $this->stickyRightColumns = $right;
    }
}