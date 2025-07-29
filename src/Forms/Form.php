<?php

namespace Mystamyst\Tablenice\Forms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Mystamyst\Tablenice\Contracts\HasFormFields;
use Mystamyst\Tablenice\Forms\Concerns\HasFields;
use Mystamyst\Tablenice\Forms\Concerns\HasValidation;

abstract class Form implements HasFormFields
{
    use HasFields, HasValidation;

    protected string $title = 'Form';

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Define the fields for the form.
     *
     * @return array< \Mystamyst\Tablenice\Forms\Fields\Field>
     */
    abstract public function getFormFields(): array;

    /**
     * Logic to create a new record.
     *
     * @param array $data
     * @return Model
     */
    abstract public function create(array $data): Model;

    /**
     * Logic to update an existing record.
     *
     * @param Model $record
     * @param array $data
     * @return Model
     */
    abstract public function update(Model $record, array $data): Model;
}