<?php

namespace App\Actions;

use App\Actions\Concerns\InteractsWithComposer;
use App\Actions\Concerns\InteractsWithNpm;
use App\Actions\Concerns\InteractsWithStubs;
use App\Actions\Concerns\ReplaceInFile;
use App\ConsoleWriter;
use App\LogsToConsole;
use App\Shell;

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

        $packageInstall = $this->shell->execInProject(sprintf(
            'composer require trovcms/faqs-module %s %s',
            config('installer.store.dev') ? ' dev-main' : '',
            config('installer.store.with_output') ? '' : '--quiet'
        ));

        $this->abortIf(! $packageInstall->isSuccessful(), 'The FAQs Module installer did not complete successfully.', $packageInstall);

        $moduleInstall = $this->shell->execInProject('php artisan trov:faqs-install');

        if (! $moduleInstall->isSuccessful()) {
            app('final-steps')->add('Run <span class="text-green-500">php artisan trov:faqs-install</span>');
            $this->consoleWriter->warn('Failed to install FAQs module into TrovCMS.');
        }

        $this->consoleWriter->success('Successfully installed FAQs Module.');
    }
}
