<?php

namespace Mystamyst\Tablenice\Contracts;

interface HasFormFields
{
    /**
     * Define the fields for the form.
     *
     * @return array< \Mystamyst\Tablenice\Forms\Fields\Field>
     */
    public function getFormFields(): array;
}