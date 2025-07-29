<?php

namespace Mystamyst\Tablenice\Columns;

class BadgeColumn extends Column
{
    protected array $colors = []; // ['value' => 'color-class']
    protected string $defaultColor = 'bg-gray-200 text-gray-800';

    public function colors(array $colors): static
    {
        $this->colors = $colors;
        return $this;
    }

    public function defaultColor(string $colorClass): static
    {
        $this->defaultColor = $colorClass;
        return $this;
    }

    public function render(\Illuminate\Database\Eloquent\Model $record)
    {
        $value = parent::getValue($record);
        $colorClass = $this->colors[$value] ?? $this->defaultColor;
        $displayValue = $this->formatUsing ? ($this->formatUsing)($value, $record) : $value;

        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$colorClass}\">{$displayValue}</span>";
    }
}