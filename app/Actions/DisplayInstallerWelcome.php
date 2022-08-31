<?php

namespace App\Actions;

use function Termwind\{render};

class DisplayInstallerWelcome
{
    protected $installerLogo = "
 _____             ___ __  __ ___ :version:
|_   _| _ _____ __/ __|  \/  / __|
  | || '_/ _ \ V / (__| |\/| \__ \
  |_||_| \___/\_/ \___|_|  |_|___/";

    public function __construct()
    {
        $this->installerLogo = str_replace(':version:', config('app.version'), $this->installerLogo);
    }

    public function __invoke()
    {
        foreach (explode("\n", $this->installerLogo) as $line) {
            // Extra space on the end fixes an issue with console when it ends with backslash
            app('console-writer')->text("<fg=#0ea5e9;bg=default>{$line} </>");
        }

        render(<<<'HTML'
            <div class="py-1 ml-1">
                <div class="px-1 bg-sky-500 text-black">TrovCMS Installer</div>
                <em class="ml-1">
                    Quickly spin up a new TrovCMS application.
                </em>
            </div>
        HTML);
    }
}
