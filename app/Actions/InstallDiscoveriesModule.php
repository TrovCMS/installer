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

class InstallDiscoveriesModule
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
        if (! config('installer.store.discoveries') && ! config('installer.store.all_modules')) {
            return;
        }

        $this->consoleWriter->logStep('Installing Discoveries Module');

        try {
            $timestamp = date('Y_m_d_His');
            $filesystem = new Filesystem;
            $migrationFiles = $filesystem->glob(config('installer.store.project_path').'/database/migrations/*');

            $migrations = collect($migrationFiles)->filter(fn ($file) => Str::contains($file, 'create_discoveries_tables'));

            if ($migrations && config('installer.store.force_create')) {
                $migrations->each(fn ($file) => $filesystem->delete($file));
            } elseif ($migrations) {
                $this->consoleWriter->warn('Discoveries module is already installed in this project.');

                if (! $this->consoleWriter->confirm('Continue with installation? This will overwrite existing module.', false)) {
                    $this->consoleWriter->note('Discoveries module installation terminated.');

                    return;
                }

                $migrations->each(fn ($file) => $filesystem->delete($file));
            }

            // Database
            $this->publishStub('database/factories/DiscoveryTopicFactory.php', 'discoveries/database/factories/DiscoveryTopicFactory.php');
            $this->publishStub('database/factories/DiscoveryArticleFactory.php', 'discoveries/database/factories/DiscoveryArticleFactory.php');
            $this->publishStub('database/migrations/'.$timestamp.'_create_discoveries_tables.php', 'discoveries/database/migrations/create_discoveries_tables.php');
            $this->publishStub('database/seeders/DiscoveryTopicSeeder.php', 'discoveries/database/seeders/DiscoveryTopicSeeder.php');
            $this->publishStub('database/seeders/DiscoveryArticleSeeder.php', 'discoveries/database/seeders/DiscoveryArticleSeeder.php');

            // Models
            $this->publishStub('app/Models/DiscoveryTopic.php', 'discoveries/models/DiscoveryTopic.php');
            $this->publishStub('app/Models/DiscoveryArticle.php', 'discoveries/models/DiscoveryArticle.php');

            // Resources
            $this->publishStubDirectory('app/Filament/Resources/', 'discoveries/resources/');

            // Controllers
            $this->publishStub('app/Http/Controllers/DiscoveryTopicController.php', 'discoveries/controllers/DiscoveryTopicController.php');
            $this->publishStub('app/Http/Controllers/DiscoveryArticleController.php', 'discoveries/controllers/DiscoveryArticleController.php');

            // Views
            $this->publishStubDirectory('resources/views/components/', 'discoveries/components/');
            $this->publishStubDirectory('resources/views/discoveries/', 'discoveries/views/');
        } catch (InstallerException $e) {
            app('console-writer')->exception('Could not install Discoveries Module.');
            $this->error($e->getMessage());
        }

        $this->consoleWriter->success('Successfully installed Discoveries Module.');
    }
}
