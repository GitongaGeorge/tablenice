<?php

namespace Mystamyst\TableNice\Columns;

use Mystamyst\TableNice\Enums\Icons\IconPosition;
use Mystamyst\TableNice\Enums\Icons\CarbonIconsIcon;
use Mystamyst\TableNice\Enums\Color;
use Mystamyst\TableNice\Enums\Icons\HeroiconsIcon;
use Mystamyst\TableNice\Enums\Icons\IconparkIcon;
use Mystamyst\TableNice\Enums\Icons\PhosphorIconsIcon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class TextColumn extends Column
{
    protected $formatCallback = null;
    protected bool $isBadge = false;
    protected $badgeColorCallback = null;
    protected bool $isHtml = false;
    protected $description = null;
    protected $icon = null;
    protected ?IconPosition $iconPosition = null;
    protected $iconColor = null; // Can be Color enum or callable
    protected string $iconSize = 'h-5 w-5';
    protected $textColor = null; // Can be Color enum or callable

    public function format(callable $callback): self
    {
        $this->formatCallback = $callback;
        return $this;
    }

    public function truncate(int $length = 50, string $end = '...'): self
    {
        return $this->format(fn ($value) => Str::limit(strip_tags($value), $length, $end));
    }

    public function toLowerCase(): self
    {
        return $this->format(fn ($value) => Str::lower($value));
    }

    public function toUpperCase(): self
    {
        return $this->format(fn ($value) => Str::upper($value));
    }

    public function toTitleCase(): self
    {
        return $this->format(fn ($value) => Str::title($value));
    }

    // CORRECTED: Parameter `$color` is now explicitly nullable.
    public function badge(?Color $color = null): self
    {
        $this->isBadge = true;
        $this->badgeColor($color ?? Color::GRAY);
        return $this;
    }

    public function badgeColor(Color|callable $callback): self
    {
        $this->badgeColorCallback = $callback;
        return $this;
    }

    public function html(bool $isHtml = true): self
    {
        $this->isHtml = $isHtml;
        return $this;
    }

    public function markdown(): self
    {
        $this->format(fn ($value) => Str::markdown($value));
        return $this->html();
    }

    public function icon($icon, IconPosition $position = IconPosition::PREFIX): self
    {
        $this->icon = $icon;
        $this->iconPosition = $position;
        return $this;
    }

    public function iconColor(Color|callable $color): self
    {
        $this->iconColor = $color;
        return $this;
    }

    public function iconSize(string $sizeClass): self
    {
        $this->iconSize = $sizeClass;
        return $this;
    }

    public function textColor(Color|callable $color): self
    {
        $this->textColor = $color;
        return $this;
    }

    public function description($description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(Model $model): ?string
    {
        if (is_callable($this->description)) {
            return call_user_func($this->description, $model);
        }
        return $this->description;
    }

    public function toHtml(Model $model): string
    {
        $rawValue = $this->resolveValue($model);
        $displayValue = $rawValue;

        if ($this->formatCallback) {
            $displayValue = call_user_func($this->formatCallback, $displayValue, $model, $this);
        }

        $tooltipAttributes = '';
        if ($this->tooltip) {
            $tooltipText = is_callable($this->tooltip) ? call_user_func($this->tooltip, $model) : $this->tooltip;
            $escapedContent = addslashes($tooltipText);
            $tooltipAttributes = sprintf(
                ' @mouseenter="$store.tooltip.show($el, \'%s\')" @mouseleave="$store.tooltip.hide()"',
                $escapedContent
            );
        }

        $iconHtml = '';
        if ($this->icon) {
            $icon = is_callable($this->icon) ? call_user_func($this->icon, $model) : $this->icon;
            if ($icon) {
                $iconColorEnum = is_callable($this->iconColor) ? call_user_func($this->iconColor, $model) : $this->iconColor;
                $iconColorClass = $this->colorEnumToTextColorClass($iconColorEnum);
                $iconComponentString = $icon->toHtml(['class' => $this->iconSize . ' ' . $iconColorClass]);
                $iconHtml = Blade::render($iconComponentString);
            }
        }
        
        $highlightedValue = $this->highlight((string) $displayValue, $this->isHtml);
        
        if ($this->isHtml) {
            $textContent = '<div class="prose prose-sm dark:prose-invert max-w-none">' . $highlightedValue . '</div>';
        } else {
            $textContent = $highlightedValue;
        }
        
        $flexClasses = 'inline-flex items-center gap-x-2';
        
        $contentHtml = ($this->iconPosition === IconPosition::SUFFIX)
            ? "<span>{$textContent}</span>" . $iconHtml
            : $iconHtml . "<span>{$textContent}</span>";

        $descriptionHtml = '';
        if ($descriptionText = $this->getDescription($model)) {
            $descriptionHtml = sprintf('<div class="text-xs text-gray-500 dark:text-gray-400 mt-1">%s</div>', e($descriptionText));
        }

        $mainContentHtml = "<div>{$contentHtml}</div>" . $descriptionHtml;
        
        $baseCellClasses = 'px-6 py-4 whitespace-nowrap ' . $this->getStickyClasses();
        $styles = $this->getStyles();
        $styleAttribute = $styles ? sprintf('style="%s"', $styles) : '';

        if ($this->isBadge) {
            $colorEnum = Color::GRAY;
            if ($this->badgeColorCallback) {
                $colorEnum = is_callable($this->badgeColorCallback) ? call_user_func($this->badgeColorCallback, $rawValue, $model) : $this->badgeColorCallback;
            }
            $colorName = strtolower($colorEnum->name);
            
            $colorClasses = match ($colorName) {
                'green' => 'bg-green-100 text-green-800 ring-green-600/20 dark:bg-green-900/50 dark:text-green-300',
                'red' => 'bg-red-100 text-red-800 ring-red-600/20 dark:bg-red-900/50 dark:text-red-300',
                'yellow' => 'bg-yellow-100 text-yellow-800 ring-yellow-600/20 dark:bg-yellow-900/50 dark:text-yellow-300',
                'blue' => 'bg-blue-100 text-blue-800 ring-blue-600/20 dark:bg-blue-900/50 dark:text-blue-300',
                'indigo' => 'bg-indigo-100 text-indigo-800 ring-indigo-600/20 dark:bg-indigo-900/50 dark:text-indigo-300',
                'purple' => 'bg-purple-100 text-purple-800 ring-purple-600/20 dark:bg-purple-900/50 dark:text-purple-300',
                'pink' => 'bg-pink-100 text-pink-800 ring-pink-600/20 dark:bg-pink-900/50 dark:text-pink-300',
                'rose' => 'bg-rose-100 text-rose-800 ring-rose-600/20 dark:bg-rose-900/50 dark:text-rose-300',
                'cyan' => 'bg-cyan-100 text-cyan-800 ring-cyan-600/20 dark:bg-cyan-900/50 dark:text-cyan-300',
                'teal' => 'bg-teal-100 text-teal-800 ring-teal-600/20 dark:bg-teal-900/50 dark:text-teal-300',
                'orange' => 'bg-orange-100 text-orange-800 ring-orange-600/20 dark:bg-orange-900/50 dark:text-orange-300',
                'slate' => 'bg-slate-100 text-slate-800 ring-slate-500/20 dark:bg-slate-700 dark:text-slate-300',
                default => 'bg-gray-100 text-gray-800 ring-gray-500/20 dark:bg-gray-700 dark:text-gray-300',
            };
            
            $finalContent = sprintf(
                '<span class="%s px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset %s">%s</span>',
                $flexClasses,
                $colorClasses,
                $contentHtml
            );
            $mainContentHtml = "<div>{$finalContent}</div>" . $descriptionHtml;
        }

        $finalCellInnerHtml = $mainContentHtml;

        if ($this->isCopyable()) {
            $rawValueForCopy = json_encode(strip_tags((string)$rawValue));
            $copyIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>';
            $checkIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
            $jsLogic = '$store.clipboard.copy(' . $rawValueForCopy . '); copied = true; window.dispatchEvent(new CustomEvent(\'show-alert\', { detail: { message: \'Copied to clipboard!\' }})); setTimeout(() => { copied = false; }, 2000);';
            $onClickAttribute = htmlspecialchars($jsLogic, ENT_QUOTES, 'UTF-8');
            $copyButtonHtml = sprintf('<div x-data="{ copied: false }" class="ml-2 flex-shrink-0"><button type="button" class="p-1 text-gray-400 rounded-full hover:bg-gray-200 dark:hover:bg-slate-600 focus:outline-none" @click="%s" @mouseenter="if (!copied) $store.tooltip.show($el, \'Copy\')" @mouseleave="$store.tooltip.hide()"><span x-show="!copied">%s</span><span x-show="copied" style="display: none;">%s</span></button></div>', $onClickAttribute, $copyIcon, $checkIcon);
            $finalCellInnerHtml = "<div class=\"flex items-center justify-between w-full\"><div class=\"flex-grow\">{$mainContentHtml}</div>{$copyButtonHtml}</div>";
        }
        
        $textColorEnum = is_callable($this->textColor) ? call_user_func($this->textColor, $model) : $this->textColor;
        $textColorClass = $this->colorEnumToTextColorClass($textColorEnum) ?: 'text-gray-800 dark:text-slate-200';
        $cellClasses = trim("{$baseCellClasses} text-sm {$textColorClass} " . $this->getAlignmentClass());

        return sprintf('<td class="%s" %s %s>%s</td>', $cellClasses, $styleAttribute, $tooltipAttributes, $finalCellInnerHtml);
    }
}
