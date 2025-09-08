<?php

namespace Mystamyst\TableNice\Forms\Fields;

class DateField extends TextInput
{
    protected string $type = 'text';

    /**
     * Explicitly define the view for this field.
     */
    protected ?string $view = 'components.forms.fields.date-field';
}

