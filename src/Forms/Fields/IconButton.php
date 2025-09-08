<?php

namespace Mystamyst\TableNice\Forms\Fields;

use App\Enums\Color;
use App\Enums\HeroiconsIcon;
use App\Enums\PhosphorIconsIcon;
use App\Enums\IconparkIcon;
use App\Enums\CarbonIconsIcon;

class IconButton extends Field
{
    protected string $type = 'button';
    protected ?object $icon = null; // Can hold any of your Icon enums
    protected ?Color $color = null;
    protected ?string $action = null;

    /**
     * Define the view for this specific field.
     */
    protected ?string $view = 'components.forms.fields.icon-button';

    /**
     * Set the button's icon using one of your enum cases.
     */
    public function icon(HeroiconsIcon|PhosphorIconsIcon|IconparkIcon|CarbonIconsIcon $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Set the button's color using your Color enum.
     */
    public function color(Color $color): self
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Set the Livewire action to be called on click.
     * e.g., ->action('save') will result in wire:click="save"
     */
    public function action(string $action): self
    {
        $this->action = $action;
        return $this;
    }
    
    /**
     * Override the default button type (default is 'button').
     */
    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    // Getters for the Blade view to use
    public function getIcon(): ?object
    {
        return $this->icon;
    }

    public function getColor(): ?Color
    {
        return $this->color;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * Renders the field's view component to HTML. This is required by the parent Field class.
     */
    public function toHtml(): string
    {
        return view($this->getView(), [
            'field' => $this,
            'isViewOnly' => $this->isDisabled()
        ])->render();
    }
}