<?php

namespace Mystamyst\TableNice\Enums;

/**
 * Defines the position of an icon relative to the text in a column.
 */
enum IconPosition: string
{
    case PREFIX = 'prefix'; // Before the text
    case SUFFIX = 'suffix'; // After the text
}
