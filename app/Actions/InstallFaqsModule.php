<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;

class InstallFaqsModule
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
        if (! config('installer.store.faqs') && ! config('installer.store.all_modules')) {
            return;
        }

        $this->consoleWriter->logStep('Installing FAQs Module');

        $moduleInstall = $this->shell->execInProject('php artisan trov:add --faqs');

        if (! $moduleInstall->isSuccessful()) {
            app('final-steps')->add('Run <span class="text-green-500">php artisan trov:add --faqs --force</span>');
            $this->consoleWriter->warn('Failed to install FAQs module into TrovCMS.');
        }

        $this->consoleWriter->success('Successfully installed FAQs Module.');
    }
}
