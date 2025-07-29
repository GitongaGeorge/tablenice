<?php

namespace Mystamyst\Tablenice\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class MakeDatatableCommand extends GeneratorCommand
{
    protected $name = 'make:datatable';
    protected $description = 'Create a new Tablenice datatable class';
    protected $type = 'Datatable';

    protected function getStub(): string
    {
        return __DIR__.'/../stubs/datatable.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Livewire\Datatables';
    }

    protected function replaceClass($stub, $name): string
    {
        $stub = parent::replaceClass($stub, $name);
        return str_replace('DummyModel', $this->argument('name'), $stub); // Replace DummyModel
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