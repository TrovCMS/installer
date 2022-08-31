<?php

namespace App\Actions;

use App\Actions\Concerns\InteractsWithComposer;
use App\Actions\Concerns\InteractsWithNpm;
use App\Actions\Concerns\InteractsWithStubs;
use App\Actions\Concerns\ReplaceInFile;
use App\ConsoleWriter;
use App\InstallerException;
use App\LogsToConsole;
use App\Shell;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class InstallFaqsModule
{
    use AbortsCommands;
    use InteractsWithComposer;
    use ReplaceInFile;
    use InteractsWithStubs;
    use InteractsWithNpm;
    use LogsToConsole;

    private $shell;

    private $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        if (! config('installer.store.faqs') && ! config('installer.store.all_modules')) {
            return;
        }

        $this->consoleWriter->logStep('Installing FAQs Module');

        try {
            $timestamp = date('Y_m_d_His');
            $filesystem = new Filesystem;
            $migrationFiles = $filesystem->glob(config('installer.store.project_path').'/database/migrations/*');

            $migrations = collect($migrationFiles)->filter(fn ($file) => Str::contains($file, 'create_faqs_table'));

            if ($migrations && config('installer.store.force_create')) {
                $migrations->each(fn ($file) => $filesystem->delete($file));
            } elseif ($migrations) {
                $this->consoleWriter->warn('FAQs module is already installed in this project.');

                if (! $this->consoleWriter->confirm('Continue with installation? This will overwrite existing module.', false)) {
                    $this->consoleWriter->note('FAQs module installation terminated.');

                    return;
                }

                $migrations->each(fn ($file) => $filesystem->delete($file));
            }

            // Database
            $this->publishStub('database/factories/FaqFactory.php', 'faqs/database/factories/FaqFactory.php');
            $this->publishStub('database/migrations/'.$timestamp.'_create_faqs_tables.php', 'faqs/database/migrations/create_faqs_table.php');
            $this->publishStub('database/seeders/FaqSeeder.php', 'faqs/database/seeders/FaqSeeder.php');

            // Models
            $this->publishStub('app/Models/Faq.php', 'faqs/models/Faq.php');

            // Resources
            $this->publishStubDirectory('app/Filament/Resources/', 'faqs/resources/');

            // Controllers
            $this->publishStub('app/Http/Controllers/FaqController.php', 'faqs/controllers/FaqController.php');

            // Views
            $this->publishStubDirectory('app/resources/views/faqs/', 'faqs/views/');
        } catch (InstallerException $e) {
            app('console-writer')->exception('Could not install FAQs Module.');
            $this->error($e->getMessage());
        }

        $this->consoleWriter->success('Successfully installed FAQs Module.');
    }
}
