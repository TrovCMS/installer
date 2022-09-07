<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;

class InstallSheetsModule
{
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

        $moduleInstall = $this->shell->execInProject('php artisan trov:add --sheets');

        if (! $moduleInstall->isSuccessful()) {
            app('final-steps')->add('Run <span class="text-green-500">php artisan trov:add --sheets --force</span>');
            $this->consoleWriter->warn('Failed to install Sheets module into TrovCMS.');
        }

        $this->consoleWriter->success('Successfully installed Sheets Module.');
    }
}
