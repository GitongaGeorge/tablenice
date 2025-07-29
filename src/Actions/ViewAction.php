<?php

namespace Mystamyst\Tablenice\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class ViewAction extends Action
{
    protected ?string $viewRoute = null; // Route name to redirect to for viewing

    public function __construct(string $name = 'view', string $label = 'View')
    {
        parent::__construct($name, $label);
        $this->icon('heroicon-o-eye');
        $this->color('secondary');
    }

    public function route(string $routeName): static
    {
        $this->viewRoute = $routeName;
        return $this;
    }

    public function handle(...$records): mixed
    {
        if (empty($records) || !($records[0] instanceof Model)) {
            Session::flash('error', 'No record provided for viewing.');
            return null;
        }

        $record = $records[0];

        if ($this->viewRoute) {
            return \redirect()->route($this->viewRoute, $record->getKey());
        } else {
            Session::flash('info', 'View route not configured for this action.');
            // Optionally, open a read-only modal or display details here
        }
        return null;
    }
}