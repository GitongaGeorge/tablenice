<?php

namespace Mystamyst\TableNice\Forms\Fields;

class DateTimeField extends TextInput
{
    protected string $type = 'datetime-local';

    /**
     * Explicitly define the view for this field.
     */
    protected ?string $view = 'components.forms.fields.datetime-field';
}
