<?php

namespace Mystamyst\TableNice\Forms\Fields;

class SelectField extends Field
{
    protected string $type = 'select';
    protected array $options = [];

    /**
     * Explicitly define the view for this field.
     */
    protected ?string $view = 'components.forms.fields.select-field';

    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function options(array $options): static
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function toHtml(): string
    {
        return '';
    }
}
