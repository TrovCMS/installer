<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;
use Symfony\Component\Process\Process;

class InstallModules
{
    use AbortsCommands;

    private $shell;

    private $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        $this->consoleWriter->logStep('Running module installer');

        if (! config('installer.store.migrate_database')) {
            app('final-steps')->add('Run <span class="text-green-500">php artisan trov:add --force</span> to add any additional modules');
            $this->consoleWriter->note('Database not set up.');

            return;
        }

        $cwd = getcwd();
        chdir(config('installer.store.project_path'));

        $process = Process::fromShellCommandline('php artisan trov:add --force');

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $output->writeln('  <bg=yellow;fg=black> WARN </> '.$e->getMessage().PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->consoleWriter->write('    '.$line);
        });

        $this->abortIf(! $process->isSuccessful(), 'Module installation failed.', $process);

        chdir($cwd);
    }
}
