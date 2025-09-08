<?php

namespace Mystamyst\TableNice\Columns;

use Mystamyst\TableNice\Enums\SortDirection;
use Mystamyst\TableNice\Enums\SummaryLocation;
use Mystamyst\TableNice\Enums\TextAlign;
use Mystamyst\TableNice\Group;
use Mystamyst\TableNice\Summaries\Contracts\Summary;
use Mystamyst\TableNice\Enums\Color;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class Column
{
    protected string $type;
    protected string $label;
    protected string $name;
    protected bool $isSearchable = false;
    protected $searchLogicCallback = null;
    protected bool $isFilterable = false;
    protected ?array $filterOptions = null;
    protected ?Group $group = null;
    protected bool $isDefaultGroup = false;
    protected ?array $summary = null;
    protected $isVisible = true;
    protected ?string $searchTerm = null;
    protected ?TextAlign $textAlign = null;
    protected bool $isSortable = false;
    protected bool $isSticky = false;
    protected $tooltip = null;
    protected ?string $width = null;
    protected bool $isCopyable = false;
    public ?array $theme = null;

    public ?int $stickyOffset = null;

    public function __construct(string $name, string $label, string $type = 'text')
    {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
    }

    public static function make(string $name, ?string $label = null): static
    {
        $label = $label ?? Str::of($name)->replace('_', ' ')->title();
        return new static($name, $label);
    }

    // ** START: NEW HELPER METHOD **
    /**
     * Converts a Color enum to its corresponding Tailwind CSS text color class.
     */
    protected function colorEnumToTextColorClass(?Color $color): string
    {
        if (!$color) return '';
        
        return match($color) {
            Color::BLUE => 'text-blue-600 dark:text-blue-400',
            Color::RED => 'text-red-600 dark:text-red-400',
            Color::GREEN => 'text-green-600 dark:text-green-400',
            Color::YELLOW => 'text-yellow-600 dark:text-yellow-400',
            Color::PURPLE => 'text-purple-600 dark:text-purple-400',
            Color::CYAN => 'text-cyan-600 dark:text-cyan-400',
            Color::PINK => 'text-pink-600 dark:text-pink-400',
            Color::INDIGO => 'text-indigo-600 dark:text-indigo-400',
            Color::EMERALD => 'text-emerald-600 dark:text-emerald-400',
            Color::ROSE => 'text-rose-600 dark:text-rose-400',
            Color::TEAL => 'text-teal-600 dark:text-teal-400',
            Color::ORANGE => 'text-orange-600 dark:text-orange-400',
            Color::SLATE => 'text-slate-600 dark:text-slate-400',
            default => 'text-gray-600 dark:text-gray-400',
        };
    }
    // ** END: NEW HELPER METHOD **

    public function width(string $width): self
    {
        $this->width = $width;
        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function tooltip(string|callable $tooltip): self
    {
        $this->tooltip = $tooltip;
        return $this;
    }

    public function permission(string $permissionName): self
    {
        $this->isVisible = fn () => Gate::allows($permissionName);
        return $this;
    }

    public function visible($condition): self
    {
        $this->isVisible = $condition;
        return $this;
    }

    public function isVisible(): bool
    {
        if (is_callable($this->isVisible)) {
            return call_user_func($this->isVisible);
        }
        return $this->isVisible;
    }

    public function sortable(bool $isSortable = true): static
    {
        $this->isSortable = $isSortable;
        return $this;
    }

    public function isSortable(): bool
    {
        return $this->isSortable;
    }

    public function sticky(bool $isSticky = true): static
    {
        $this->isSticky = $isSticky;
        return $this;
    }

    public function isSticky(): bool
    {
        return $this->isSticky;
    }

    public function copyable(bool $isCopyable = true): static
    {
        $this->isCopyable = $isCopyable;
        return $this;
    }

    public function isCopyable(): bool
    {
        return $this->isCopyable;
    }

    public function setStickyOffset(int $offset): self
    {
        $this->stickyOffset = $offset;
        return $this;
    }

    protected function getStyles(): string
    {
        $styles = [];
        if ($this->isSticky() && !is_null($this->stickyOffset)) {
            $styles[] = "left: {$this->stickyOffset}px";
        }
        if (!is_null($this->width)) {
            $styles[] = "min-width: {$this->width}";
        }
        return implode('; ', $styles);
    }

    protected function getStickyClasses(): string
    {
        if (!$this->isSticky()) {
            return '';
        }
        $stickyBg = $this->theme['stickyCellBg'] ?? 'bg-slate-50 dark:bg-slate-800/50';
        $stickyHoverBg = $this->theme['stickyCellHoverBg'] ?? 'group-hover:bg-slate-100 dark:group-hover:bg-slate-700/50';

        return 'sticky z-10 ' . $stickyBg . ' ' . $stickyHoverBg;
    }

    public function textAlign(TextAlign $align): static
    {
        $this->textAlign = $align;
        return $this;
    }

    public function getAlignmentClass(): string
    {
        return $this->textAlign?->value ?? '';
    }

    public function group(SortDirection $direction = SortDirection::ASC, ?string $format = null): static
    {
        $this->group = Group::make($this->name, $this->label, $direction, $format);
        return $this;
    }

    public function defaultGroup(SortDirection $direction = SortDirection::ASC, ?string $format = null): static
    {
        $this->group($direction, $format);
        $this->isDefaultGroup = true;
        return $this;
    }

    public function isGroupable(): bool
    {
        return $this->group !== null;
    }

    public function isDefaultGroup(): bool
    {
        return $this->isDefaultGroup;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function searchable(bool|callable $condition = true): static
    {
        if (is_callable($condition)) {
            $this->isSearchable = true;
            $this->searchLogicCallback = $condition;
        } else {
            $this->isSearchable = $condition;
        }
        return $this;
    }

    public function isSearchable(): bool
    {
        return $this->isSearchable;
    }

    public function filterable(?array $options = null, bool $isFilterable = true): static
    {
        $this->isFilterable = $isFilterable;
        $this->filterOptions = $options;
        return $this;
    }

    public function isFilterable(): bool
    {
        return $this->isFilterable;
    }

    public function getFilterOptions(): ?array
    {
        return $this->filterOptions;
    }

    public function summary(
        Summary|callable $calculator,
        SummaryLocation $location = SummaryLocation::BOTTOM,
        ?string $groupLabel = '{value}',
        ?string $totalLabel = '{value}'
    ): static {
        $this->summary = [
            'calculator' => $calculator,
            'location' => $location,
            'group_label' => $groupLabel,
            'total_label' => $totalLabel,
        ];
        return $this;
    }

    public function hasSummary(): bool
    {
        return $this->summary !== null;
    }

    public function getSummary(): ?array
    {
        return $this->summary;
    }

    public function setSearchTerm(?string $term): self
    {
        $this->searchTerm = $term;
        return $this;
    }

    public function highlight(string $text, bool $isHtml = false): string
    {
        if (empty($this->searchTerm) || empty($text)) {
            return $isHtml ? $text : e($text);
        }
    
        $searchTerm = $this->searchTerm;
        $placeholders = [];
        $textWithPlaceholders = preg_replace_callback('/<[^>]*>/', function ($matches) use (&$placeholders) {
            $placeholder = '##PLACEHOLDER_' . count($placeholders) . '##';
            $placeholders[$placeholder] = $matches[0];
            return $placeholder;
        }, $text);
    
        $parts = preg_split('/(' . preg_quote($searchTerm, '/') . ')/i', $textWithPlaceholders, -1, PREG_SPLIT_DELIM_CAPTURE);
    
        $highlightedText = '';
        $highlightClass = $this->theme['highlight'] ?? 'bg-yellow-200 text-yellow-800 dark:bg-yellow-500/50 dark:text-yellow-100';

        foreach ($parts as $part) {
            if (strcasecmp($part, $searchTerm) === 0) {
                $highlightedText .= '<mark class="px-1 rounded ' . $highlightClass . '">' . e($part) . '</mark>';
            } else {
                $highlightedText .= $isHtml ? $part : e($part);
            }
        }
    
        $finalHtml = str_replace(array_keys($placeholders), array_values($placeholders), $highlightedText);
    
        return $finalHtml;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return $this->type;
    }

    protected function resolveValue(Model $model)
    {
        return data_get($model, $this->getName());
    }

    public function searchLogic(Builder $query, string $searchTerm): void
    {
        if (is_callable($this->searchLogicCallback)) {
            call_user_func($this->searchLogicCallback, $query, $searchTerm);
        } else {
            $query->orWhere($this->getName(), 'like', '%' . $searchTerm . '%');
        }
    }

    public function filterLogic(Builder $query, $value): void
    {
        $query->where($this->getName(), $value);
    }

    public function sortLogic(Builder $query, string $direction): Builder
    {
        return $query->orderBy($this->getName(), $direction);
    }

    public function toHtml(Model $model): string
    {
        $value = $this->resolveValue($model);
        $tooltipAttributes = '';

        if ($this->tooltip) {
            $tooltipText = is_callable($this->tooltip) ? call_user_func($this->tooltip, $model) : $this->tooltip;
            $escapedContent = addslashes($tooltipText);
            $tooltipAttributes = sprintf(
                ' @mouseenter="$store.tooltip.show($el, \'%s\')" @mouseleave="$store.tooltip.hide()"',
                $escapedContent
            );
        }

        $baseClasses = 'px-6 py-4 text-gray-500 dark:text-slate-400';
        $finalClasses = trim(sprintf('%s %s %s', $baseClasses, $this->getAlignmentClass(), $this->getStickyClasses()));
        
        $displayValue = $this->highlight((string) $value);

        $styles = $this->getStyles();
        $styleAttribute = $styles ? sprintf('style="%s"', $styles) : '';

        return sprintf(
            '<td class="%s" %s %s>%s</td>',
            $finalClasses,
            $styleAttribute,
            $tooltipAttributes,
            $displayValue
        );
    }
}