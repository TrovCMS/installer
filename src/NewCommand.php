<?php

namespace Trov\Installer\Console;

use RuntimeException;
use function Termwind\{render};
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Create a new Trov CMS application')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addOption('dev', null, InputOption::VALUE_NONE, 'Installs the latest "development" release')
            ->addOption('git', null, InputOption::VALUE_NONE, 'Initialize a Git repository')
            ->addOption('branch', null, InputOption::VALUE_REQUIRED, 'The branch that should be created for a new repository', $this->defaultBranch())
            ->addOption('github', null, InputOption::VALUE_OPTIONAL, 'Create a new repository on GitHub', false)
            ->addOption('organization', null, InputOption::VALUE_REQUIRED, 'The GitHub organization to create the new repository for')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Forces install even if the directory already exists')
            ->addOption('faqs', null, InputOption::VALUE_NONE, 'Install FAQs Module')
            ->addOption('discoveries', null, InputOption::VALUE_NONE, 'Install Discovery Center Module (Topic and Articles)')
            ->addOption('airport', null, InputOption::VALUE_NONE, 'Install the Airport Module (Landing Pages)')
            ->addOption('sheets', null, InputOption::VALUE_NONE, 'Install Sheets Module (Unbranded Pages)');
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

        $continue = true;

        $name = $input->getArgument('name');

        $directory = $name !== '.' ? getcwd().'/'.$name : '.';

        $version = Utils::getVersion($input);

        if (! $input->getOption('force')) {
            $this->verifyApplicationDoesntExist($directory);
        }

        if ($input->getOption('force') && $directory === '.') {
            throw new RuntimeException('Cannot use --force option when using current directory for installation!');
        }

        $composer = Utils::findComposer();

        render('<div class="text-green-500">Installing Trov CMS...</div>');

        $commands = [
            // $composer." create-project laravel/laravel \"$directory\" $version --remove-vcs --prefer-dist --quiet",
            $composer . " create-project trovcms/trov \"$directory\" $version --repository '{\"type\": \"path\", \"url\": \"~/Sites/trovcms\", \"options\": {\"symlink\": false}}' --remove-vcs --prefer-dist --quiet"
        ];

        if ($directory != '.' && $input->getOption('force')) {
            if (PHP_OS_FAMILY == 'Windows') {
                array_unshift($commands, "(if exist \"$directory\" rd /s /q \"$directory\")");
            } else {
                array_unshift($commands, "rm -rf \"$directory\"");
            }
        }

        if (PHP_OS_FAMILY != 'Windows') {
            $commands[] = "chmod 755 \"$directory/artisan\"";
        }

        if (($process = Utils::runCommands($commands, $input, $output))->isSuccessful()) {
            if ($name !== '.') {
                Utils::replaceInFile(
                    'APP_URL=http://localhost',
                    'APP_URL=http://'.$name.'.test',
                    $directory.'/.env'
                );

                Utils::replaceInFile(
                    'DB_DATABASE=laravel',
                    'DB_DATABASE='.str_replace('-', '_', strtolower($name)),
                    $directory.'/.env'
                );

                Utils::replaceInFile(
                    'DB_DATABASE=laravel',
                    'DB_DATABASE='.str_replace('-', '_', strtolower($name)),
                    $directory.'/.env.example'
                );
            }
        }

        /**
         * Install Filament, Settings and Packages
         */
        if ($continue) {
            if ($input->getOption('airport')) {
                render('<div class="text-green-500">Installing Airport Module...</div>');
            }

            if ($input->getOption('discoveries')) {
                render('<div class="text-green-500">Installing Discovery Center Module...</div>');
            }

            if ($input->getOption('faqs')) {
                render('<div class="text-green-500">Installing FAQs Module...</div>');
            }

            if ($input->getOption('sheets')) {
                render('<div class="text-green-500">Installing Sheets Module...</div>');
            }
        }

        /**
         * Commit and push to Github
         */
        if ($input->getOption('git') || $input->getOption('github') !== false) {
            render('<div class="text-green-500">Creating repository...</div>');
            $this->createRepository($directory, $input, $output);
        }

        if ($input->getOption('github') !== false) {
            render('<div class="text-green-500">Pushing to GitHub...</div>');
            $this->pushToGitHub($name, $directory, $input, $output);
            $output->writeln('');
        }

        render('<div class="bg-green-300 px-1 mt-1 font-bold text-green-900">New Trov CMS project installed!</div>');

        render('<div class="mt-1 text-green-500">Next Steps</div>');

        render(<<<HTML
            <ol class="pl-2">
                <li>cd {$name}</li>
                <li>php artisan migrate</li>
                <li>php artisan shield:install</li>
                <li>Login at <a href="http://{$name}.test/admin/login">http://{$name}.test/admin/login</a></li>
            </ol>
        HTML);

        return $process->getExitCode();
    }

    /**
     * Return the local machine's default Git branch if set or default to `main`.
     *
     * @return string
     */
    protected function defaultBranch()
    {
        $process = new Process(['git', 'config', '--global', 'init.defaultBranch']);

        $process->run();

        $output = trim($process->getOutput());

        return $process->isSuccessful() && $output ? $output : 'main';
    }

    /**
     * Create a Git repository and commit the base Laravel skeleton.
     *
     * @param  string  $directory
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    protected function createRepository(string $directory, InputInterface $input, OutputInterface $output)
    {
        chdir($directory);

        $branch = $input->getOption('branch') ?: $this->defaultBranch();

        $commands = [
            'git init -q',
            'git add .',
            'git commit -q -m "Set up a fresh Laravel app"',
            "git branch -M {$branch}",
        ];

        $this->runCommands($commands, $input, $output);
    }

    /**
     * Commit any changes in the current working directory.
     *
     * @param  string  $message
     * @param  string  $directory
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    protected function commitChanges(string $message, string $directory, InputInterface $input, OutputInterface $output)
    {
        if (! $input->getOption('git') && $input->getOption('github') === false) {
            return;
        }

        chdir($directory);

        $commands = [
            'git add .',
            "git commit -q -m \"$message\"",
        ];

        $this->runCommands($commands, $input, $output);
    }

    /**
     * Create a GitHub repository and push the git log to it.
     *
     * @param  string  $name
     * @param  string  $directory
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    protected function pushToGitHub(string $name, string $directory, InputInterface $input, OutputInterface $output)
    {
        $process = new Process(['gh', 'auth', 'status']);
        $process->run();

        if (! $process->isSuccessful()) {
            $output->writeln('  <bg=yellow;fg=black> WARN </> Make sure the "gh" CLI tool is installed and that you\'re authenticated to GitHub. Skipping...'.PHP_EOL);

            return;
        }

        chdir($directory);

        $name = $input->getOption('organization') ? $input->getOption('organization')."/$name" : $name;
        $flags = $input->getOption('github') ?: '--private';
        $branch = $input->getOption('branch') ?: $this->defaultBranch();

        $commands = [
            "gh repo create {$name} --source=. --push {$flags}",
        ];

        $this->runCommands($commands, $input, $output, ['GIT_TERMINAL_PROMPT' => 0]);
    }

    /**
     * Verify that the application does not already exist.
     *
     * @param  string  $directory
     * @return void
     */
    protected function verifyApplicationDoesntExist($directory)
    {
        if ((is_dir($directory) || is_file($directory)) && $directory != getcwd()) {
            throw new RuntimeException('Application already exists!');
        }
    }
}
