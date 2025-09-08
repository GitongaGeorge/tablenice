<?php

namespace Mystamyst\TableNice\Forms\Fields;

class RichEditorField extends Field
{
    protected string $type = 'rich-editor';
    
    /**
     * Explicitly define the view for this field to prevent auto-detection errors.
     */
    protected ?string $view = 'components.forms.fields.rich-editor-field';

    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    /**
     * Rendering is handled by the Blade component.
     */
    public function toHtml(): string
    {
        return '';
    }
}
