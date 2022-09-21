<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;
use App\Tools\Database;
use PDOException;

class SeedDemoData
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
        if (! config('installer.store.demo')) {
            return;
        }

        $this->consoleWriter->logStep('Running demo seeder');

        if (! config('installer.store.migrate_database')) {
            $this->consoleWriter->note('Database not set up.');
            app('final-steps')->add('Run <span class="text-green-500">php artisan db:seed --class=DemoSeeder</span>');

            return;
        }

        try {
            $this->database
                ->fillFromInstallerStore(config('installer.store'))
                ->ensureExists(config('installer.store.database_name'));

            $process = $this->shell->execInProject("php artisan db:seed --class=DemoSeeder{$this->withQuiet()}");

            return $process->isSuccessful()
                ? $this->consoleWriter->success('Database seeded')
                : $this->consoleWriter->warn("Failed to run {$process->getCommandLine()}");
        } catch (PDOException $e) {
            $this->consoleWriter->warn($e->getMessage());

            return $this->consoleWriter->warn($this->failureMigrateError());
        }
    }

    protected function failureMigrateError(): string
    {
        return sprintf(
            "Skipping demo database seeding using credentials <fg=yellow>mysql://%s:****@%s:%s</>\nYou will need to run the database seed manually.",
            config('installer.store.database_username'),
            config('installer.store.database_host'),
            config('installer.store.database_port')
        );
    }

    private function withQuiet()
    {
        return config('installer.store.with_output') ? '' : ' --quiet';
    }
}
