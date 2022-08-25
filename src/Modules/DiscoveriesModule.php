<?php

namespace Trov\Installer\Console\Modules;

use function Termwind\{render};
use Trov\Installer\Console\Utils;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DiscoveriesModule extends Command
{
    protected string $moduleName = 'Discoveries';

    public function install(string $directory, InputInterface $input, OutputInterface $output): int
    {
        $filesystem = new Filesystem();
        $finder = new Finder();
        $io = new SymfonyStyle($input, $output);

        $timestamp = date('Y_m_d_His');
        $basePath = __DIR__ . '/../../stubs/discoveries';

        render('<div class="text-green-500 mt-1">Installing '. $this->moduleName .' Module...</div>');

        $isInstalled = $finder->files()->in($directory . '/database/migrations/')->name('*_create_discoveries_tables.php');
        $files = iterator_to_array($isInstalled);

        if ($files) {
            render('<div class="text-red-500">'. $this->moduleName .' module is already installed in this project.</div>');
            if (! $io->confirm('Continue with installation? This will overwrite existing module.', false)) {
                return Command::FAILURE;
            }

            $filesystem->remove(array_values($files)[0]->getPathname());
        }

        $filesystem->copy($basePath . '/models/DiscoveryTopic.php', $directory . '/app/Models/DiscoveryTopic.php');
        $filesystem->copy($basePath . '/models/DiscoveryArticle.php', $directory . '/app/Models/DiscoveryArticle.php');
        $filesystem->copy($basePath . '/database/factories/DiscoveryTopicFactory.php', $directory . '/database/factories/DiscoveryTopicFactory.php');
        $filesystem->copy($basePath . '/database/factories/DiscoveryArticleFactory.php', $directory . '/database/factories/DiscoveryArticleFactory.php');
        $filesystem->copy($basePath . '/database/migrations/create_discoveries_tables.php', $directory . '/database/migrations/'.$timestamp.'_create_discoveries_tables.php');
        $filesystem->copy($basePath . '/database/seeders/DiscoveryTopicSeeder.php', $directory . '/database/seeders/DiscoveryTopicSeeder.php');
        $filesystem->copy($basePath . '/database/seeders/DiscoveryArticleSeeder.php', $directory . '/database/seeders/DiscoveryArticleSeeder.php');
        $filesystem->copy($basePath . '/forms/blocks/Infographic.php', $directory . '/app/Forms/Blocks/Infographic.php');
        $filesystem->copy($basePath . '/forms/components/DiscoveryPageBuilder.php', $directory . '/app/Forms/Components/DiscoveryPageBuilder.php');
        $filesystem->copy($basePath . '/view/Infographic.php', $directory . '/app/View/Blocks/Infographic.php');
        $filesystem->copy($basePath . '/views/infographic.blade.php', $directory . '/resources/views/components/blocks/infographic.blade.php');
        $filesystem->mirror($basePath . '/resources', $directory . '/app/Filament/Resources');

        render('<div class="text-green-500">'. $this->moduleName .' Module successfully installed!</div>');

        return Command::SUCCESS;
    }
}
