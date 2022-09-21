<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;

class CreateTrovCMSProject
{
    use AbortsCommands;

    protected $shell;

    protected $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        $this->consoleWriter->logStep('Creating a new TrovCMS project');

        $command = 'composer create-project trovcms/trovcms %s%s --remove-vcs --prefer-dist %s';

        $process = $this->shell->execInRoot(sprintf(
            $command,
            config('installer.store.project_name'),
            config('installer.store.dev') ? ' dev-main' : '',
            config('installer.store.with_output') ? '' : ' --quiet'
        ));

        $this->abortIf(! $process->isSuccessful(), 'The TrovCMS installer did not complete successfully.', $process);

        $this->consoleWriter->success(sprintf(
            "A new TrovCMS application has been created from the %s branch at '%s'.",
            config('installer.store.dev') ? 'develop' : 'release',
            config('installer.store.project_name')
        ));
    }
}
