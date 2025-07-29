<?php

namespace Mystamyst\Tablenice\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;

class CustomAction extends Action
{
    protected Closure $actionCallback;

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
    }

    public static function make(string $name, string $label): static
    {
        return new static($name, $label);
    }

    public function setActionCallback(Closure $actionCallback): static
    {
        $this->actionCallback = $actionCallback;
        return $this;
    }
    public function handle(...$records): mixed
    {
        if ($this->before instanceof Closure) {
            ($this->before)(...$records);
        }

        $result = ($this->actionCallback)(...$records);

        if ($this->after instanceof Closure) {
            ($this->after)(...$records);
        }

        return $result;
    }
}