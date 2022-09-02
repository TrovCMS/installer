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

class InstallSheetsModule
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
        if (! config('installer.store.sheets') && ! config('installer.store.all_modules')) {
            return;
        }

        $this->consoleWriter->logStep('Installing Sheets Module');

        try {
            $timestamp = date('Y_m_d_His');
            $filesystem = new Filesystem;
            $migrationFiles = $filesystem->glob(config('installer.store.project_path').'/database/migrations/*');

            $migrations = collect($migrationFiles)->filter(fn ($file) => Str::contains($file, 'create_sheets_table'));

            if ($migrations && config('installer.store.force_create')) {
                $migrations->each(fn ($file) => $filesystem->delete($file));
            } elseif ($migrations) {
                $this->consoleWriter->warn('Sheets module is already installed in this project.');

                if (! $this->consoleWriter->confirm('Continue with installation? This will overwrite existing module.', false)) {
                    $this->consoleWriter->note('Sheets module installation terminated.');

                    return;
                }

                $migrations->each(fn ($file) => $filesystem->delete($file));
            }

            // Database
            $this->publishStub('database/factories/SheetFactory.php', 'sheets/database/factories/SheetFactory.php');
            $this->publishStub('database/migrations/'.$timestamp.'_create_sheets_tables.php', 'sheets/database/migrations/create_sheets_table.php');
            $this->publishStub('database/seeders/SheetSeeder.php', 'sheets/database/seeders/SheetSeeder.php');

            // Models
            $this->publishStub('app/Models/Sheet.php', 'sheets/models/Sheet.php');

            // Resources
            $this->publishStubDirectory('app/Filament/Resources/', 'sheets/resources/');

            // Controllers
            $this->publishStub('app/Http/Controllers/SheetController.php', 'sheets/controllers/SheetController.php');

            // Views
            $this->publishStubDirectory('resources/views/sheets/', 'sheets/views/');
        } catch (InstallerException $e) {
            app('console-writer')->exception('Could not install Sheets Module.');
            $this->error($e->getMessage());
        }

        $this->consoleWriter->success('Successfully installed Sheets Module.');
    }
}
