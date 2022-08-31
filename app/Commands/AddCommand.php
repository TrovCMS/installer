<?php

namespace App\Commands;

class AddCommand extends InstallerCommand
{
    protected $signature = 'add';

    protected $description = 'Add a module to an existing project';

    public function handle()
    {
        return self::SUCCESS;
    }
}
