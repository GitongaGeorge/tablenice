<?php

namespace Mystamyst\TableNice\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tablenice:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the TableNice resources and provide setup instructions.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Installing TableNice...');

        $this->comment('Publishing configuration file...');
        $this->call('vendor:publish', [
            '--provider' => 'Mystamyst\TableNice\TableNiceServiceProvider',
            '--tag' => 'tablenice-config'
        ]);

        $this->comment('Publishing views for customization (optional)...');
        $this->call('vendor:publish', [
            '--provider' => 'Mystamyst\TableNice\TableNiceServiceProvider',
            '--tag' => 'tablenice-views'
        ]);

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

