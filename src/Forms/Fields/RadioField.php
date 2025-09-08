<?php

namespace Mystamyst\TableNice\Forms\Fields;

use Mystamyst\TableNice\Enums\IconPosition;

class RadioField extends Field
{
    protected string $type = 'radio';
    protected array $options = [];
    protected IconPosition $iconPosition = IconPosition::PREFIX;
    protected ?string $view = 'components.forms.fields.radio-field';

    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    /**
     * Define the options for the radio field.
     */
    public function options(array $options): static
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Set the position of the icon relative to the label.
     */
    public function iconPosition(IconPosition $position): static
    {
        $this->iconPosition = $position;
        return $this;
    }

    public function getIconPosition(): IconPosition
    {
        return $this->iconPosition;
    }
}