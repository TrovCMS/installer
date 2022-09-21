<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;
use App\Tools\Database;
use Symfony\Component\Process\Process;

class InstallTrovCMS
{
    use AbortsCommands;

    protected $shell;

    protected $database;

    protected $consoleWriter;

    public function __construct(Shell $shell, Database $database, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->database = $database;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        $this->consoleWriter->logStep('Finishing TrovCMS installation');

        if (! config('installer.store.migrate_database')) {
            app('final-steps')->add('Run <span class="text-green-500">php artisan trov:install --force</span>');
            $this->consoleWriter->note('Database not set up.');

            return;
        }

        $cwd = getcwd();
        chdir(config('installer.store.project_path'));

        $process = Process::fromShellCommandline('php artisan trov:install --force');

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

        $this->abortIf(! $process->isSuccessful(), 'TrovCMS installation failed.', $process);

        chdir($cwd);
    }
}
