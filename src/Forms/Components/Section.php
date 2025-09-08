<?php

namespace Mystamyst\TableNice\Forms\Components;

use Mystamyst\TableNice\Enums\Icons\HeroiconsIcon;
use Mystamyst\TableNice\Enums\Icons\CarbonIconsIcon;
use Mystamyst\TableNice\Enums\Icons\IconparkIcon;
use Mystamyst\TableNice\Enums\Icons\PhosphorIconsIcon;
use Illuminate\Support\Facades\Gate;

class Section
{
    protected string $title;
    protected ?string $subtitle = null;
    protected HeroiconsIcon|CarbonIconsIcon|IconparkIcon|PhosphorIconsIcon|null $icon = null;
    protected array $schema = [];
    /**
     * @var bool|callable
     */
    protected $isVisible = true;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public static function make(string $title): static
    {
        return new static($title);
    }

    /**
     * Set the permission required to see this section.
     */
    public function permission(string $permissionName): self
    {
        $this->isVisible = fn () => Gate::allows($permissionName);
        return $this;
    }

    /**
     * Set the visibility of the section.
     */
    public function visible($condition): self
    {
        $this->isVisible = $condition;
        return $this;
    }

    /**
     * Determine if the section is currently visible.
     */
    public function isVisible(): bool
    {
        if (is_callable($this->isVisible)) {
            return call_user_func($this->isVisible);
        }

        return $this->isVisible;
    }

    public function subtitle(?string $subtitle): static
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    public function icon(HeroiconsIcon|CarbonIconsIcon|IconparkIcon|PhosphorIconsIcon|null $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function schema(array $fields): static
    {
        $this->schema = $fields;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function getIcon(): HeroiconsIcon|CarbonIconsIcon|IconparkIcon|PhosphorIconsIcon|null
    {
        return $this->icon;
    }

    public function getSchema(): array
    {
        return $this->schema;
    }
}
