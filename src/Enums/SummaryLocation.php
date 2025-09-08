<?php

namespace Mystamyst\TableNice\Enums;

/**
 * Defines where the summary row should be displayed.
 */
enum SummaryLocation: string
{
    case TOP = 'top';
    case BOTTOM = 'bottom';
    case BOTH = 'both';
}
