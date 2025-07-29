<?php

namespace Mystamyst\Tablenice\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class MakeFormCommand extends GeneratorCommand
{
    protected $name = 'make:tablenice-form';
    protected $description = 'Create a new Tablenice form class';
    protected $type = 'Form';

    protected function getStub(): string
    {
        return __DIR__.'/../stubs/form.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Forms';
    }

    protected function replaceClass($stub, $name): string
    {
        $stub = parent::replaceClass($stub, $name);
        return str_replace('DummyModel', $this->argument('name'), $stub); // Placeholder for model
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the form class.'],
        ];
    }
}