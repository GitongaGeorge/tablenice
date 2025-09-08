<?php

namespace Mystamyst\TableNice\Actions;

use Illuminate\Database\Eloquent\Model;

class ViewAction extends Action
{
    public function __construct(string $name = 'view')
    {
        parent::__construct($name);
        $this->disabled();
    }

    /**
     * Static factory method for cleaner instantiation.
     */
    public static function make(string $name = 'view'): static
    {
        return new static($name);
    }

    /**
     * This action is for viewing only, so it performs no operation.
     */
    public function runOnModel(Model $model, array $data = [])
    {
        // No operation is needed for a view action.
    }
}
