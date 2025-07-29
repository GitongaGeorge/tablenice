<?php

namespace Mystamyst\Tablenice\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeColumnCommand extends GeneratorCommand
{
    protected $name = 'make:tablenice-column';
    protected $description = 'Create a new Tablenice column class';
    protected $type = 'Column';

    protected function getStub(): string
    {
        return __DIR__.'/../stubs/column.stub';
    }

    // --- UPDATED: Default namespace for Columns ---
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Columns'; // A general namespace for column definitions
    }
}