<?php

namespace App\Actions\Concerns;

use Illuminate\Support\Facades\File;

trait InteractsWithStubs
{
    protected function publishStub(string $file, string $stub): void
    {
        $output = config('installer.store.project_path').'/'.ltrim($file, '/');

        File::put($output, File::get(base_path('stubs/'.ltrim($stub, '/'))));
    }

    protected function publishStubDirectory(string $directory, string $stub): void
    {
        $output = config('installer.store.project_path').'/'.ltrim($directory, '/');

        File::copyDirectory(base_path('stubs/'.ltrim($stub, '/')), $output);
    }
}
