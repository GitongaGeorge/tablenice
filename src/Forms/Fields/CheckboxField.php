<?php

namespace Mystamyst\TableNice\Forms\Fields;

class CheckboxField extends Field
{
    protected string $type = 'checkbox';

    /**
     * Explicitly define the view for this field.
     */
    protected ?string $view = 'components.forms.fields.checkbox-field';

    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function toHtml(): string
    {
        return '';
    }
}
