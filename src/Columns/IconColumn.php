<?php

namespace Mystamyst\Tablenice\Columns;

class IconColumn extends Column
{
    protected array $icons = []; // ['value' => 'heroicon-o-icon-name']
    protected string $defaultIcon = 'heroicon-o-question-mark-circle';
    protected array $iconColors = []; // ['value' => 'text-color-class']
    protected string $defaultIconColor = 'text-gray-400';

    public function icons(array $icons): static
    {
        $this->icons = $icons;
        return $this;
    }

    public function defaultIcon(string $iconName): static
    {
        $this->defaultIcon = $iconName;
        return $this;
    }

    public function iconColors(array $colors): static
    {
        $this->iconColors = $colors;
        return $this;
    }

    public function defaultIconColor(string $colorClass): static
    {
        $this->defaultIconColor = $colorClass;
        return $this;
    }

    public function render(\Illuminate\Database\Eloquent\Model $record)
    {
        $value = parent::getValue($record);
        $icon = $this->icons[$value] ?? $this->defaultIcon;
        $color = $this->iconColors[$value] ?? $this->defaultIconColor;

        // Assuming you have Heroicons setup in your main app, e.g., via Blade UI Kit or directly
        return "<span class=\"{$color}\"><x-heroicon-o-{$icon} class=\"h-5 w-5\" /></span>";
    }
}