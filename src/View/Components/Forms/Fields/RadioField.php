<?php

namespace App\View\Components\Forms\Fields;

use Mystamyst\TableNice\Forms\Fields\RadioField as RadioFieldObject;
use Illuminate\View\Component;

class RadioField extends Component
{
    public RadioFieldObject $field;
    public bool $isViewOnly;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(RadioFieldObject $field, bool $isViewOnly = false)
    {
        $this->field = $field;
        $this->isViewOnly = $isViewOnly;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.forms.fields.radio-field');
    }
}