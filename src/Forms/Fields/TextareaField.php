<?php

namespace Mystamyst\TableNice\Forms\Fields;

class TextareaField extends Field
{
    protected string $type = 'textarea';

    /**
     * Explicitly define the view for this field.
     */
    protected ?string $view = 'components.forms.fields.textarea-field';

    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function toHtml(): string
    {
        return '';
    }
}
