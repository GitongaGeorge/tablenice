<?php

namespace Mystamyst\TableNice;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Mystamyst\TableNice\Commands\InstallCommand;
use Mystamyst\TableNice\Commands\MakeDatatableCommand;
use Mystamyst\TableNice\Commands\MakeDatatableFormCommand;
use Mystamyst\TableNice\Livewire\ActionForm;
use Mystamyst\TableNice\Livewire\Alert;
use Mystamyst\TableNice\Livewire\ConfirmationModal;
use Mystamyst\TableNice\Livewire\DatatableComponent;
use Mystamyst\TableNice\Livewire\FormModal;

class TableNiceServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/tablenice.php', 'tablenice'
        );
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tablenice');

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeDatatableCommand::class,
                MakeDatatableFormCommand::class,
                InstallCommand::class, // Register the new install command
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/tablenice.php' => config_path('tablenice.php'),
        ], 'tablenice-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/tablenice'),
        ], 'tablenice-views');

        // Register Livewire components with a prefix
        Livewire::component('tablenice-datatable', DatatableComponent::class);
        Livewire::component('tablenice-action-form', ActionForm::class);
        Livewire::component('tablenice-alert', Alert::class);
        Livewire::component('tablenice-confirmation-modal', ConfirmationModal::class);
        Livewire::component('tablenice-form-modal', FormModal::class);
    }
}

