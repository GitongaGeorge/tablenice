<?php

namespace Mystamyst\TableNice\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'tablenice:install';
    protected $description = 'Install all of the TableNice resources and provide setup instructions.';

    public function handle()
    {
        $this->info('Installing TableNice...');

        $this->comment('Publishing configuration file...');
        $this->call('vendor:publish', [
            '--provider' => 'Mystamyst\TableNice\TableNiceServiceProvider',
            '--tag' => 'tablenice-config'
        ]);

        $this->comment('Publishing page component stubs for full-page tables...');
        if (!File::exists(app_path('Livewire'))) {
            File::makeDirectory(app_path('Livewire'));
        }
        if (!File::exists(resource_path('views/livewire'))) {
            File::makeDirectory(resource_path('views/livewire'));
        }
        File::copy(__DIR__.'/stubs/datatable.page.stub', app_path('Livewire/DatatablePage.php'));
        File::copy(__DIR__.'/stubs/datatablepageb.stub', resource_path('views/livewire/datatable-page.blade.php'));
        $this->info('Published [app/Livewire/DatatablePage.php] and [resources/views/livewire/datatable-page.blade.php]');

        $this->info('TableNice scaffolding installed successfully.');
        $this->warn("\nNext steps are required:");

        $this->line("\n<fg=yellow>1. Add global components to your main layout file</> (e.g., <fg=gray>resources/views/layouts/app.blade.php</>)");
        $this->line('   Place these lines just before your closing `</body>` tag:');
        $this->line('   <fg=cyan><livewire:tablenice-alert /></>');
        $this->line('   <fg=cyan><livewire:tablenice-form-modal /></>');
        $this->line('   <fg=cyan><livewire:tablenice-confirmation-modal /></>');

        $this->line("\n<fg=yellow>2. Install required NPM packages</>");
        $this->line('   Run the following command in your terminal:');
        $this->line('   <fg=cyan>npm install chart.js dayjs litepicker trix</>');

        $this->line("\n<fg=yellow>3. Import JavaScript modules</>");
        $this->line("   Add the following imports to your main JavaScript file (e.g., <fg=gray>resources/js/app.js</>):");
        $this->line("   <fg=cyan>import Chart from 'chart.js/auto';</>");
        $this->line("   <fg=cyan>import 'litepicker/dist/plugins/mobilefriendly';</>");
        $this->line("   <fg=cyan>import Litepicker from 'litepicker';</>");
        $this->line("   <fg=cyan>import dayjs from 'dayjs';</>");
        $this->line("   <fg=cyan>import 'trix';</>");
        $this->line("\n   <fg=gray>// Make libraries globally available for Alpine.js components</>");
        $this->line("   <fg=cyan>window.Chart = Chart;</>");
        $this->line("   <fg=cyan>window.Litepicker = Litepicker;</>");
        $this->line("   <fg=cyan>window.dayjs = dayjs;</>");

        return self::SUCCESS;
    }
}

