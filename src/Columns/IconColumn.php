<?php

namespace Mystamyst\TableNice\Columns;

use App\Enums\CarbonIconsIcon;
use App\Enums\Color; // Use Color enum
use App\Enums\HeroiconsIcon;
use App\Enums\IconparkIcon;
use App\Enums\PhosphorIconsIcon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;

class IconColumn extends Column
{
    protected $icon = null;
    protected $iconColor = null; // Can be Color enum or callable
    protected $iconSize = 'h-6 w-6';

    public function __construct(string $name, ?string $label)
    {
        parent::__construct($name, $label ?? '', 'icon');
    }

    public function icon($icon): self
    {
        $this->icon = $icon;
        return $this;
    }
    
    // ** MODIFIED: Accepts Color enum **
    public function color(Color|callable $color): self
    {
        $this->iconColor = $color;
        return $this;
    }

    public function size(string|callable $sizeClass): self
    {
        $this->iconSize = $sizeClass;
        return $this;
    }

    public function toHtml(Model $model): string
    {
        $icon = is_callable($this->icon) ? call_user_func($this->icon, $model) : $this->icon;
        
        if (!$icon) {
            return '<td></td>';
        }

        $colorEnum = is_callable($this->iconColor) ? call_user_func($this->iconColor, $model) : $this->iconColor;
        $colorClass = $this->colorEnumToTextColorClass($colorEnum) ?: 'text-gray-500 dark:text-gray-400';
        $size = is_callable($this->iconSize) ? call_user_func($this->iconSize, $model) : $this->iconSize;

        $iconComponentString = $icon->toHtml(['class' => $size . ' ' . $colorClass]);
        $renderedIconHtml = Blade::render($iconComponentString);
        
        $alignmentClass = $this->getAlignmentClass() ?: 'text-center';

        $tooltipAttributes = '';
        if ($this->tooltip) {
            $tooltipText = is_callable($this->tooltip) ? call_user_func($this->tooltip, $model) : $this->tooltip;
            $escapedContent = addslashes($tooltipText);
            $tooltipAttributes = sprintf(
                ' @mouseenter="$store.tooltip.show($el, \'%s\')" @mouseleave="$store.tooltip.hide()"',
                $escapedContent
            );
        }
        
        $finalClasses = trim(sprintf(
            'px-6 py-4 whitespace-nowrap text-sm %s %s',
            $alignmentClass,
            $this->getStickyClasses()
        ));

        $styles = $this->getStyles();
        $styleAttribute = $styles ? sprintf('style="%s"', $styles) : '';

        return sprintf(
            '<td class="%s" %s %s>%s</td>',
            $finalClasses,
            $styleAttribute,
            $tooltipAttributes,
            $renderedIconHtml
        );
    }
}