<?php

namespace Trov\Installer\Console\Modules;

use function Termwind\{render};
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class SheetModule extends Command
{
    protected string $moduleName = 'Sheets';

    public function install(string $directory, InputInterface $input, OutputInterface $output): int
    {
        $filesystem = new Filesystem();
        $finder = new Finder();
        $io = new SymfonyStyle($input, $output);

        $timestamp = date('Y_m_d_His');
        $basePath = __DIR__ . '/../../stubs/sheets';

        render('<div class="text-green-500 mt-1">Installing '. $this->moduleName .' Module...</div>');

        $isInstalled = $finder->files()->in($directory . '/database/migrations/')->name('*_create_sheets_table.php');
        $files = iterator_to_array($isInstalled);

        if ($files) {
            render('<div class="text-red-500">'. $this->moduleName .' module is already installed in this project.</div>');
            if (! $io->confirm('Continue with installation? This will overwrite existing module.', false)) {
                return Command::FAILURE;
            }

            $filesystem->remove(array_values($files)[0]->getPathname());
        }

        $filesystem->copy($basePath . '/models/Sheet.php', $directory . '/app/Models/Sheet.php');
        $filesystem->copy($basePath . '/database/factories/SheetFactory.php', $directory . '/database/factories/SheetFactory.php');
        $filesystem->copy($basePath . '/database/migrations/create_sheets_table.php', $directory . '/database/migrations/'.$timestamp.'_create_sheets_table.php');
        $filesystem->copy($basePath . '/database/seeders/SheetSeeder.php', $directory . '/database/seeders/SheetSeeder.php');
        $filesystem->mirror($basePath . '/resources', $directory . '/app/Filament/Resources');

        render('<div class="text-green-500">'. $this->moduleName .' Module successfully installed!</div>');

        return Command::SUCCESS;
    }
}
