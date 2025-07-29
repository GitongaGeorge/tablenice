<?php

namespace Mystamyst\Tablenice\Columns;

class BooleanColumn extends Column
{
    protected string $trueIcon = 'heroicon-o-check-circle';
    protected string $falseIcon = 'heroicon-o-x-circle';
    protected string $trueColor = 'text-green-500';
    protected string $falseColor = 'text-red-500';

    public function trueIcon(string $icon): static
    {
        $this->trueIcon = $icon;
        return $this;
    }

    public function falseIcon(string $icon): static
    {
        $this->falseIcon = $icon;
        return $this;
    }

    public function trueColor(string $color): static
    {
        $this->trueColor = $color;
        return $this;
    }

    public function falseColor(string $color): static
    {
        $this->falseColor = $color;
        return $this;
    }

    public function render(\Illuminate\Database\Eloquent\Model $record)
    {
        $value = parent::getValue($record);
        $isTrue = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        $icon = $isTrue ? $this->trueIcon : $this->falseIcon;
        $color = $isTrue ? $this->trueColor : $this->falseColor;

        // Using Blade icon component if you set one up, otherwise raw SVG or text.
        // For simplicity, let's assume a basic span for now.
        return "<span class=\"{$color}\"><x-heroicon-o-{$icon} class=\"h-5 w-5\" /></span>";
    }
}