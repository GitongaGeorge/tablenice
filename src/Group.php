<?php

namespace Mystamyst\TableNice;

use Mystamyst\TableNice\Enums\SortDirection;
use Illuminate\Support\Str;

/**
 * Represents a grouping configuration for the datatable.
 */
class Group
{
    public readonly string $name;
    public readonly string $label;
    public readonly SortDirection $direction;
    public readonly ?string $format;

    public function __construct(string $name, ?string $label = null, SortDirection $direction = SortDirection::ASC, ?string $format = null)
    {
        $this->name = $name;
        $this->label = $label ?? Str::of($name)->replace('_', ' ')->title()->__toString();
        $this->direction = $direction;
        $this->format = $format;
    }

    /**
     * Static factory method for cleaner instantiation.
     */
    public static function make(string $name, ?string $label = null, SortDirection $direction = SortDirection::ASC, ?string $format = null): static
    {
        return new static($name, $label, $direction, $format);
    }

    /**
     * Static factory method for grouping by the latest records.
     */
    public static function latest(string $name, ?string $label = null, ?string $format = null): static
    {
        return new static($name, $label, SortDirection::DESC, $format);
    }
}
