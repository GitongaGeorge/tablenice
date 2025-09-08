<?php

namespace App\View\Components\Datatable;

use Illuminate\View\Component;

class FormSchema extends Component
{
    public array $schema;
    public bool $isViewOnly;
    public array $theme;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(array $schema, bool $isViewOnly = false, array $theme = [])
    {
        $this->schema = $schema;
        $this->isViewOnly = $isViewOnly;
        $this->theme = $theme;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.datatable.form-schema');
    }
}
