<?php

namespace Mystamyst\TableNice\Actions;

use Illuminate\Database\Eloquent\Model;

class EditAction extends Action
{
    protected ?string $successMessage = 'Record updated successfully.';

    public function __construct(string $name = 'edit')
    {
        parent::__construct($name);
    }

    /**
     * Static factory method for cleaner instantiation.
     */
    public static function make(string $name = 'edit'): static
    {
        return new static($name);
    }

    /**
     * The signature of this method now matches the parent Action class.
     */
    public function runOnModel(Model $model, array $data = [])
    {
        $model->update($data);
    }
}
