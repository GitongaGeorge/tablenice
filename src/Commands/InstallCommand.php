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

        $this->comment('Publishing view files...');
        $this->call('vendor:publish', [
            '--provider' => 'Mystamyst\TableNice\TableNiceServiceProvider',
            '--tag' => 'tablenice-views',
            '--force' => true,
        ]);
        $this->info('Published TableNice view files to [resources/views/vendor/tablenice/]');

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

        $this->provideTailwindInstructions();

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

        File::copy(__DIR__.'/stubs/datatable.page.stub', app_path('Livewire/DatatablePage.php'));
        File::copy(__DIR__.'/stubs/datatablepageb.stub', resource_path('views/livewire/datatable-page.blade.php'));

        $this->info('Published [app/Livewire/DatatablePage.php] and [resources/views/livewire/datatable-page.blade.php]');
    }

    protected function provideTailwindInstructions()
    {
        $tailwindVersion = $this->detectTailwindVersion();
        
        $this->line("\n   Then, add the typography plugin:");
        
        if ($tailwindVersion === 4) {
            $this->line('   <fg=green>Tailwind CSS v4 detected:</> Add this line to your <fg=gray>resources/css/app.css</> file:');
            $this->line('   <fg=cyan>@plugin "@tailwindcss/typography";</>');
        } elseif ($tailwindVersion === 3) {
            $this->line('   <fg=green>Tailwind CSS v3 detected:</> Add the typography plugin to your <fg=gray>tailwind.config.js</> file:');
            $this->line("   <fg=cyan>plugins: [require('@tailwindcss/typography')],</>");
        } else {
            $this->line('   <fg=yellow>Could not detect Tailwind CSS version.</> Choose the appropriate method:');
            $this->line('   <fg=cyan>• For Tailwind v4:</> Add <fg=gray>@plugin "@tailwindcss/typography";</> to your <fg=gray>resources/css/app.css</> file');
            $this->line('   <fg=cyan>• For Tailwind v3:</> Add <fg=gray>require(\'@tailwindcss/typography\')</> to the plugins array in <fg=gray>tailwind.config.js</>');
        }
    }

    protected function detectTailwindVersion(): ?int
    {
        // Check if tailwind.config.js exists (v3 style)
        if (File::exists(base_path('tailwind.config.js'))) {
            return 3;
        }

        // Check package.json for tailwind version
        $packageJsonPath = base_path('package.json');
        if (File::exists($packageJsonPath)) {
            $packageJson = json_decode(File::get($packageJsonPath), true);
            
            $tailwindVersion = $packageJson['devDependencies']['tailwindcss'] ?? 
                             $packageJson['dependencies']['tailwindcss'] ?? null;
            
            if ($tailwindVersion) {
                // Extract major version number from version string (e.g., "^4.0.0" -> 4)
                if (preg_match('/[\^~]?(\d+)/', $tailwindVersion, $matches)) {
                    return (int) $matches[1];
                }
            }
        }

        // Check if app.css contains @tailwind directives without config file (likely v4)
        $appCssPath = resource_path('css/app.css');
        if (File::exists($appCssPath) && !File::exists(base_path('tailwind.config.js'))) {
            $appCssContent = File::get($appCssPath);
            if (str_contains($appCssContent, '@tailwind') || str_contains($appCssContent, '@import "tailwindcss"')) {
                return 4;
            }
        }

        return null;
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