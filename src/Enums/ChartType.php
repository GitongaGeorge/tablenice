<?php

namespace Mystamyst\TableNice\Enums;

/**
 * Defines the types of charts available for cards.
 */
enum ChartType: string
{
    case BAR = 'bar';
    case LINE = 'line';
    case DOUGHNUT = 'doughnut';
    case PIE = 'pie';
}
