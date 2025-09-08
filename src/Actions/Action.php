<?php

namespace Mystamyst\TableNice\Actions;

use App\DataTables\Enums\IconPosition;
use App\DataTables\Forms\Form;
use App\Enums\CarbonIconsIcon;
use App\Enums\Color; // ** USE the new Color enum **
use App\Enums\HeroiconsIcon;
use App\Enums\IconparkIcon;
use App\Enums\PhosphorIconsIcon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

abstract class Action
{
    protected string $name;
    protected ?string $label = null;
    protected $icon = null;
    protected ?IconPosition $iconPosition = IconPosition::PREFIX;
    protected ?Color $color = null; // ** MODIFIED: Use Color enum type **
    protected ?string $textColor = null;
    protected ?string $displayType = null;
    protected ?string $form = null;
    protected string $iconSize = 'h-5 w-5';
    protected ?string $successMessage = null;
    protected $successIcon = null;
    protected bool $isFormDisabled = false;
    protected string $modalSize = '2xl';
    protected bool $requiresConfirmation = false;
    protected $permission = true;
    protected $tooltip = null;
    protected bool $isIconOnly = false;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }
    
    public function displayAs(string $type): static
    {
        $this->displayType = $type;
        return $this;
    }

    public function getDisplayType(): ?string
    {
        return $this->displayType;
    }

    public function textColor(?string $color): static
    {
        $this->textColor = $color;
        return $this;
    }

    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    public function tooltip(string|callable $tooltip): self
    {
        $this->tooltip = $tooltip;
        return $this;
    }

    public function getTooltip(Model $model): ?string
    {
        if (is_callable($this->tooltip)) {
            return call_user_func($this->tooltip, $model);
        }
        return $this->tooltip;
    }

    public function permission($permission): self
    {
        $this->permission = $permission;
        return $this;
    }

    public function isVisible(): bool
    {
        if (is_string($this->permission)) {
            return Gate::allows($this->permission);
        }
        if (is_callable($this->permission)) {
            return call_user_func($this->permission);
        }

        return (bool) $this->permission;
    }

    public function requiresConfirmation(bool $requires = true): static
    {
        $this->requiresConfirmation = $requires;
        return $this;
    }

    public function getRequiresConfirmation(): bool
    {
        return $this->requiresConfirmation;
    }

    public function modalSize(string $size): static
    {
        $this->modalSize = $size;
        return $this;
    }

    public function getModalSize(): string
    {
        return $this->modalSize;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label ?? str($this->name)->title()->toString();
    }

    public function label(?string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function icon($icon, IconPosition $position = IconPosition::PREFIX): static
    {
        $this->icon = $icon;
        $this->iconPosition = $position;
        return $this;
    }
    
    public function iconOnly(bool $isIconOnly = true): static
    {
        $this->isIconOnly = $isIconOnly;
        return $this;
    }

    public function isIconOnly(): bool
    {
        return $this->isIconOnly;
    }

    public function iconPosition(IconPosition $position): static
    {
        $this->iconPosition = $position;
        return $this;
    }

    public function getIconPosition(): IconPosition
    {
        return $this->iconPosition;
    }

    public function color(?Color $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function getColor(): ?Color
    {
        return $this->color;
    }

    public function getIconHtml(): ?string
    {
        if (!$this->icon) {
            return null;
        }

        $classes = $this->isIconOnly ? $this->iconSize : 'h-4 w-4';
        if ($this->textColor) {
             $classes .= ' ' . $this->textColor;
        }

        $iconComponentString = $this->icon->toHtml(['class' => $classes]);
        return Blade::render($iconComponentString);
    }

    public function getForm(): ?Form
    {
        return $this->form ? app($this->form) : null;
    }

    public function form(?string $form): static
    {
        $this->form = $form;
        return $this;
    }

    public function successMessage(?string $message): static
    {
        $this->successMessage = $message;
        return $this;
    }

    public function getSuccessMessage(): ?string
    {
        return $this->successMessage;
    }

    public function successIcon($icon): static
    {
        $this->successIcon = $icon;
        return $this;
    }

    public function getSuccessIcon()
    {
        return $this->successIcon;
    }
    
    public function disabled(bool $disabled = true): static
    {
        $this->isFormDisabled = $disabled;
        return $this;
    }

    public function isFormDisabled(): bool
    {
        return $this->isFormDisabled;
    }

    /**
     * Set the action to be displayed as an outlined button.
     */
    public function outlined(bool $isOutlined = true): static
    {
        if ($isOutlined) {
            $this->displayType = 'outline';
        }
        return $this;
    }

    /**
     * Set the action to be displayed as a solid button.
     */
    public function button(bool $isButton = true): static
    {
        if ($isButton) {
            $this->displayType = 'button';
        }
        return $this;
    }

    /**
     * Generates button classes based on display type and color.
     */
    public function getButtonClasses(string $defaultDisplayType = null): string
    {
        $color = $this->getColor();

        // If no explicit color is set, return an empty string. The Blade view will handle applying theme defaults.
        if (!$color) {
            return '';
        }

        $actionColor = strtolower($color->name);
        $displayType = $this->getDisplayType() ?: $defaultDisplayType;
        $textColor = $this->getTextColor() ?? '';

        $buttonClasses = '';

        if ($displayType === 'button') {
            $buttonClasses = 'text-white ' . match ($actionColor) {
                'blue' => 'bg-blue-500 hover:bg-blue-600',
                'red' => 'bg-red-500 hover:bg-red-600',
                'green' => 'bg-green-500 hover:bg-green-600',
                'yellow' => 'bg-yellow-500 hover:bg-yellow-600',
                'purple' => 'bg-purple-500 hover:bg-purple-600',
                'cyan' => 'bg-cyan-500 hover:bg-cyan-600',
                'pink' => 'bg-pink-500 hover:bg-pink-600',
                'indigo' => 'bg-indigo-500 hover:bg-indigo-600',
                'emerald' => 'bg-emerald-500 hover:bg-emerald-600',
                'rose' => 'bg-rose-500 hover:bg-rose-600',
                'teal' => 'bg-teal-500 hover:bg-teal-600',
                'orange' => 'bg-orange-500 hover:bg-orange-600',
                'slate' => 'bg-slate-500 hover:bg-slate-600',
                default => 'bg-gray-500 hover:bg-gray-600',
            };
        } elseif ($displayType === 'outline') {
            $buttonClasses = 'bg-transparent ring-1 ' . match ($actionColor) {
                'blue' => 'ring-blue-500 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/50',
                'red' => 'ring-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/50',
                'green' => 'ring-green-500 text-green-500 hover:bg-green-50 dark:hover:bg-green-900/50',
                'yellow' => 'ring-yellow-500 text-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-900/50',
                'purple' => 'ring-purple-500 text-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900/50',
                'cyan' => 'ring-cyan-500 text-cyan-500 hover:bg-cyan-50 dark:hover:bg-cyan-900/50',
                'pink' => 'ring-pink-500 text-pink-500 hover:bg-pink-50 dark:hover:bg-pink-900/50',
                'indigo' => 'ring-indigo-500 text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/50',
                'emerald' => 'ring-emerald-500 text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/50',
                'rose' => 'ring-rose-500 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/50',
                'teal' => 'ring-teal-500 text-teal-500 hover:bg-teal-50 dark:hover:bg-teal-900/50',
                'orange' => 'ring-orange-500 text-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/50',
                'slate' => 'ring-slate-500 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700',
                default => 'ring-gray-500 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-900/50',
            };
        }

        return trim($buttonClasses . ' ' . $textColor);
    }

    abstract public function runOnModel(Model $model, array $data = []);
}

