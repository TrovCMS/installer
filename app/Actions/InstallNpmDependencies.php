<?php

namespace App\Actions;

use App\Actions\Concerns\InteractsWithNpm;
use App\ConsoleWriter;
use App\Shell;

class InstallNpmDependencies
{
    use AbortsCommands;
    use InteractsWithNpm;

    protected $shell;

    protected $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        $this->consoleWriter->logStep('Installing node dependencies (Yea, this will take a while)');

        if (! config('installer.store.with_node')) {
            app('final-steps')->add('Run <span class="text-green-500">npm install && npm run build</span>');
            $this->consoleWriter->note('Node dependencies skipped.');

            return;
        }

        $this->installAndCompileNodeDependencies();

        $this->consoleWriter->success('Npm dependencies installed.');
    }

    public function withQuiet()
    {
        return config('installer.store.with_output') ? '' : ' --silent';
    }
}
