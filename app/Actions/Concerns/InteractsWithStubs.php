<?php

namespace App\Actions\Concerns;

use Illuminate\Support\Facades\File;

trait InteractsWithStubs
{
    protected function publishStub(string $file, string $stub): void
    {
        $projectPath = config('installer.store.project_path');

        File::put($projectPath.'/'.ltrim($file, '/'), File::get(base_path('stubs/'.ltrim($stub, '/'))));
    }

    protected function publishStubDirectory(string $directory, string $stub): void
    {
        $projectPath = config('installer.store.project_path');

        File::copyDirectory(base_path('stubs/'.ltrim($stub, '/')), $projectPath.'/'.ltrim($directory, '/'));
    }
}
