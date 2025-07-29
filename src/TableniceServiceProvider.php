<?php

namespace Mystamyst\Tablenice;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Mystamyst\Tablenice\Console\Commands\MakeColumnCommand;
use Mystamyst\Tablenice\Console\Commands\MakeDatatableCommand;
use Mystamyst\Tablenice\Console\Commands\MakeFormCommand;
use Mystamyst\Tablenice\Forms\Components\Button as LivewireButtonComponent;
use Mystamyst\Tablenice\Forms\Components\Modal as LivewireModalComponent;
use Mystamyst\Tablenice\Forms\Components\Section as LivewireSectionComponent;
use Mystamyst\Tablenice\Forms\Components\Wizard as LivewireWizardComponent;

class TableniceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/tablenice.php', 'tablenice'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load package views and tag them for publishing
        $this->loadViewsFrom(__DIR__.'/Views', 'tablenice');

        $this->publishes([
            __DIR__.'/../config/tablenice.php' => config_path('tablenice.php'),
        ], 'tablenice-config');

        $this->publishes([
            __DIR__.'/Views' => resource_path('views/vendor/tablenice'),
        ], 'tablenice-views');

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeColumnCommand::class,
                MakeDatatableCommand::class,
                MakeFormCommand::class,
            ]);
        }

        // Register Livewire and Blade components
        $this->registerLivewireComponents();
        $this->registerBladeComponents();
    }

    /**
     * Register the Livewire components.
     */
    protected function registerLivewireComponents()
    {
        Livewire::component('tablenice::forms.modal', LivewireModalComponent::class);
        Livewire::component('tablenice::forms.button', LivewireButtonComponent::class);
        Livewire::component('tablenice::forms.wizard', LivewireWizardComponent::class);
        Livewire::component('tablenice::forms.section', LivewireSectionComponent::class);
    }

    /**
     * Register the Blade components for <x-tag> syntax.
     */
    protected function registerBladeComponents()
    {
        // --- FIXED: Added explicit Blade component registration for general UI elements ---
        // This allows you to use <x-tablenice-button /> etc. for simple, non-Livewire components.
        Blade::component('tablenice::forms.button', 'tablenice-button');
        Blade::component('tablenice::forms.modal', 'tablenice-modal');
        Blade::component('tablenice::forms.wizard', 'tablenice-wizard');
        Blade::component('tablenice::forms.section', 'tablenice-section');

        // Datatable components
        Blade::component('tablenice::components.datatable.table', 'tablenice-table');
        Blade::component('tablenice::components.datatable.header', 'tablenice-table-header');
        Blade::component('tablenice::components.datatable.footer', 'tablenice-table-footer');
        Blade::component('tablenice::components.datatable.filters', 'tablenice-table-filters');
        Blade::component('tablenice::components.datatable.column-selector', 'tablenice-table-column-selector');
        Blade::component('tablenice::components.datatable.pagination', 'tablenice-table-pagination');
        Blade::component('tablenice::components.datatable.actions', 'tablenice-table-actions');
        Blade::component('tablenice::components.datatable.tabs', 'tablenice-table-tabs');

        // Form field components
        Blade::component('tablenice::forms.text-input', 'tablenice-text-input');
        Blade::component('tablenice::forms.select-field', 'tablenice-select-field');
        Blade::component('tablenice::forms.checkbox-field', 'tablenice-checkbox-field');
        Blade::component('tablenice::forms.date-field', 'tablenice-date-field');
        Blade::component('tablenice::forms.datetime-field', 'tablenice-datetime-field');
        Blade::component('tablenice::forms.radio-field', 'tablenice-radio-field');
        Blade::component('tablenice::forms.textarea-field', 'tablenice-textarea-field');
    }
}
