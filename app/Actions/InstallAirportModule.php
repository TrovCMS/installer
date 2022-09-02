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

class InstallAirportModule
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
        if (! config('installer.store.airport') && ! config('installer.store.all_modules')) {
            return;
        }

        $this->consoleWriter->logStep('Installing Airport Module');

        try {
            $timestamp = date('Y_m_d_His');
            $filesystem = new Filesystem;
            $migrationFiles = $filesystem->glob(config('installer.store.project_path').'/database/migrations/*');

            $migrations = collect($migrationFiles)->filter(fn ($file) => Str::contains($file, 'create_runways_table'));

            if ($migrations && config('installer.store.force_create')) {
                $migrations->each(fn ($file) => $filesystem->delete($file));
            } elseif ($migrations) {
                $this->consoleWriter->warn('Airport module is already installed in this project.');

                if (! $this->consoleWriter->confirm('Continue with installation? This will overwrite existing module.', false)) {
                    $this->consoleWriter->note('Airport module installation terminated.');

                    return;
                }

                $migrations->each(fn ($file) => $filesystem->delete($file));
            }

            // Database
            $this->publishStub('database/factories/RunwayFactory.php', 'airport/database/factories/RunwayFactory.php');
            $this->publishStub('database/migrations/'.$timestamp.'_create_runways_tables.php', 'airport/database/migrations/create_runways_table.php');
            $this->publishStub('database/seeders/RunwaySeeder.php', 'airport/database/seeders/RunwaySeeder.php');

            // Models
            $this->publishStub('app/Models/Runway.php', 'airport/models/Runway.php');

            // Resources
            $this->publishStubDirectory('app/Filament/Resources/', 'airport/resources/');

            // Controllers
            $this->publishStub('app/Http/Controllers/AirportController.php', 'airport/controllers/AirportController.php');

            // Views
            $this->publishStubDirectory('resources/views/airport/', 'airport/views/');
        } catch (InstallerException $e) {
            app('console-writer')->exception('Could not install Airport Module.');
            $this->error($e->getMessage());
        }

        $this->consoleWriter->success('Successfully installed Airport Module.');
    }
}
