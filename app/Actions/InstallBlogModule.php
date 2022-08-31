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

class InstallBlogModule
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
        if (! config('installer.store.blog') && ! config('installer.store.all_modules')) {
            return;
        }

        $this->consoleWriter->logStep('Installing Blog Module');

        try {
            $timestamp = date('Y_m_d_His');
            $filesystem = new Filesystem;
            $migrationFiles = $filesystem->glob(config('installer.store.project_path').'/database/migrations/*');

            $migrations = collect($migrationFiles)->filter(fn ($file) => Str::contains($file, 'create_posts_table'));

            if ($migrations && config('installer.store.force_create')) {
                $migrations->each(fn ($file) => $filesystem->delete($file));
            } elseif ($migrations) {
                $this->consoleWriter->warn('Blog module is already installed in this project.');

                if (! $this->consoleWriter->confirm('Continue with installation? This will overwrite existing module.', false)) {
                    $this->consoleWriter->note('Blog module installation terminated.');

                    return;
                }

                $migrations->each(fn ($file) => $filesystem->delete($file));
            }

            // Database
            $this->publishStub('database/factories/PostFactory.php', 'blog/database/factories/PostFactory.php');
            $this->publishStub('database/migrations/'.$timestamp.'_create_posts_tables.php', 'blog/database/migrations/create_posts_table.php');
            $this->publishStub('database/seeders/PostSeeder.php', 'blog/database/seeders/PostSeeder.php');

            // Models
            $this->publishStub('app/Models/Post.php', 'blog/models/Post.php');

            // Resources
            $this->publishStubDirectory('app/Filament/Resources/', 'blog/resources/');

            // Controllers
            $this->publishStub('app/Http/Controllers/PostController.php', 'blog/controllers/PostController.php');

            // Views
            $this->publishStubDirectory('app/resources/views/blog/', 'blog/views/');
        } catch (InstallerException $e) {
            app('console-writer')->exception('Could not install Blog Module.');
            $this->error($e->getMessage());
        }

        $this->consoleWriter->success('Successfully installed Blog Module.');
    }
}
