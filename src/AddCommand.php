<?php

namespace Trov\Installer\Console;

use RuntimeException;
use function Termwind\{render};
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('add')
            ->setDescription('Add Module(s) into a Trov CMS application')
            ->addOption('faqs', null, InputOption::VALUE_NONE, 'Install FAQs Module')
            ->addOption('discoveries', null, InputOption::VALUE_NONE, 'Install Discovery Center Module (Topic and Articles)')
            ->addOption('airport', null, InputOption::VALUE_NONE, 'Install the Airport Module (Landing Pages)')
            ->addOption('sheets', null, InputOption::VALUE_NONE, 'Install Sheets Module (Unbranded Pages)')
            ->addOption('blog', null, InputOption::VALUE_NONE, 'Install Blog Module');
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        sleep(1);

        $directory = getcwd();

        $isInstalled = is_dir($directory . '/vendor/trovcms');

        if (! $isInstalled) {
            render('<div class="text-red-500 mt-1">TrovCMS is not installed in this project. Please cd to an existing TrovCMS project and try again.</div>');
            return Command::FAILURE;
        }

        if ($input->getOption('airport')) {
            (new Modules\AirportModule())->install($directory, $input, $output);
        }

        if ($input->getOption('discoveries')) {
            (new Modules\DiscoveriesModule())->install($directory, $input, $output);
        }

        if ($input->getOption('faqs')) {
            (new Modules\FaqModule())->install($directory, $input, $output);
        }

        if ($input->getOption('sheets')) {
            (new Modules\SheetModule())->install($directory, $input, $output);
        }

        if ($input->getOption('blog')) {
            (new Modules\BlogModule())->install($directory, $input, $output);
        }

        return Command::SUCCESS;
    }
}
