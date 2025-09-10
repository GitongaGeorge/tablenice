<?php

namespace Mystamyst\TableNice\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    protected $signature = 'tablenice:install';
    protected $description = 'Install all of the TableNice resources and dependencies.';

    public function handle()
    {
        $this->info('Installing TableNice...');

        $this->comment('Publishing configuration file...');
        $this->call('vendor:publish', [
            '--provider' => 'Mystamyst\TableNice\TableNiceServiceProvider',
            '--tag' => 'tablenice-config',
            '--force' => true,
        ]);

        $this->comment('Publishing page component stubs for full-page tables...');
        $this->publishStubs();

        $this->installIconPackages();

        $this->info('TableNice scaffolding installed successfully!');
        $this->warn("\nFinal manual steps are required:");

        $this->line("\n<fg=yellow>1. Add global components and scripts stack to your main layout file</> (e.g., <fg=gray>resources/views/components/layouts/app.blade.php</>)");
        $this->line('   Place these lines just before your closing `</body>` tag:');
        $this->line('   <fg=cyan><livewire:tablenice-alert /></>');
        $this->line('   <fg=cyan><livewire:tablenice-form-modal /></>');
        $this->line('   <fg=cyan><livewire:tablenice-confirmation-modal /></>');
        $this->line('   <fg=cyan>@stack(\'tablenice-scripts\')</>');

        $this->line("\n<fg=yellow>2. Install and configure frontend dependencies</>");
        $this->line('   Run the following command in your terminal:');
        $this->line('   <fg=cyan>npm install -D chart.js dayjs litepicker trix @tailwindcss/typography @popperjs/core</>');
        $this->line("\n   Then, add the typography plugin to your <fg=gray>tailwind.config.js</> file:");
        $this->line("   <fg=cyan>plugins: [require('@tailwindcss/typography')],</>");


        return self::SUCCESS;
    }

    protected function publishStubs()
    {
        if (! File::exists(app_path('Livewire'))) {
            File::makeDirectory(app_path('Livewire'));
        }
        if (! File::exists(resource_path('views/livewire'))) {
            File::makeDirectory(resource_path('views/livewire'));
        }

        File::copy(__DIR__.'/stubs/DatatablePage.php.stub', app_path('Livewire/DatatablePage.php'));
        File::copy(__DIR__.'/stubs/datatable-page.blade.php.stub', resource_path('views/livewire/datatable-page.blade.php'));

        $this->info('Published [app/Livewire/DatatablePage.php] and [resources/views/livewire/datatable-page.blade.php]');
    }

    protected function installIconPackages()
    {
        $this->line("\n<fg=yellow>Verifying Icon Dependencies...</>");

        $packages = [
            'blade-ui-kit/blade-icons',
            'blade-ui-kit/blade-heroicons',
            'codeat3/blade-phosphor-icons',
            'codeat3/blade-carbon-icons',
            'codeat3/blade-iconpark',
        ];

        if ($this->confirm('This package requires several icon libraries. Would you like to install them now via Composer?', true)) {
            $this->comment('Installing icon packages. This may take a moment...');

            $command = array_merge(['composer', 'require'], $packages);

            $process = new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']);
            $process->setTimeout(null)->run(fn ($type, $buffer) => $this->output->write($buffer));

            if (! $process->isSuccessful()) {
                $this->error('Failed to install icon packages. Please install them manually and run `php artisan icons:cache`.');
                return;
            }

            $this->info('Icon packages installed successfully.');
            $this->comment('Caching icons for optimal performance...');
            $this->call('icons:cache');
            $this->info('Icons cached successfully.');
        } else {
            $this->warn('Skipping icon package installation. Please ensure they are installed manually for icons to work correctly.');
        }
    }
}

