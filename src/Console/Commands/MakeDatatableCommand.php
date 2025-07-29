<?php

namespace Mystamyst\Tablenice\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Str; // Added for Str::singular and class_basename

class MakeDatatableCommand extends GeneratorCommand
{
    protected $name = 'make:datatable';
    protected $description = 'Create a new Tablenice datatable class';
    protected $type = 'Datatable';

    protected function getStub(): string
    {
        return __DIR__.'/../stubs/datatable.stub';
    }

    // --- UPDATED: Default namespace for Livewire Datatables ---
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Livewire\Datatables'; // Livewire 3 convention
    }

    protected function replaceClass($stub, $name): string
    {
        $stub = parent::replaceClass($stub, $name);
        // Assuming the model name is derived from the datatable name, e.g., UsersTable -> User
        $modelName = Str::singular(str_replace('Table', '', class_basename($this->argument('name'))));
        return str_replace('DummyModel', $modelName, $stub);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the datatable class.'],
        ];
    }
}