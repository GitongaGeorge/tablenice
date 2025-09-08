<?php

namespace Mystamyst\TableNice\Actions;

use App\Enums\HeroiconsIcon;
use Illuminate\Database\Eloquent\Model;

class DeleteAction extends Action
{
    protected ?string $successMessage = 'Record deleted successfully.';
    protected $successIcon = HeroiconsIcon::S_CHECK_CIRCLE;

    public function __construct(string $name = 'delete')
    {
        parent::__construct($name);
    }

    /**
     * Static factory method for cleaner instantiation.
     */
    public static function make(string $name = 'delete'): static
    {
        return new static($name);
    }

    /**
     * The signature of this method now matches the parent Action class.
     */
    public function runOnModel(Model $model, array $data = [])
    {
        $model->delete();
    }
}
