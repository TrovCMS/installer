<?php

namespace App\Actions;

use App\Actions\Concerns\InteractsWithComposer;
use App\Actions\Concerns\InteractsWithNpm;
use App\Actions\Concerns\InteractsWithStubs;
use App\Actions\Concerns\ReplaceInFile;
use App\ConsoleWriter;
use App\Shell;
use Illuminate\Support\Facades\File;

class RevertToMix
{
    use AbortsCommands;
    use InteractsWithComposer;
    use ReplaceInFile;
    use InteractsWithStubs;
    use InteractsWithNpm;

    private $shell;

    private $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        if (! config('installer.store.mix') || ! File::exists(config('installer.store.project_path').'/vite.config.js')) {
            return;
        }

        $this->consoleWriter->logStep('Reverting to Laravel Mix');

        $this->publishStub('package.json', 'mix/package.json');
        $this->publishStub('webpack.mix.js', 'mix/webpack.mix.js');
        $this->publishStub('resources/js/bootstrap.js', 'mix/bootstrap.js');

        $process = $this->shell->execInProject('rm vite.config.js');

        if (! $process->isSuccessful()) {
            app('final-steps')->add('Delete vite.config.js');
            $this->warn('Failed to delete vite.config.js.');
        }

        $this->consoleWriter->success('Successfully reverted to Laravel Mix.');
    }
}
