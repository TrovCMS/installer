<?php

namespace App\Actions\Concerns;

use Illuminate\Support\Facades\File;

trait ReplaceInFile
{
    protected function replaceInProjectFile(string $search, string $replace, string $file)
    {
        $projectPath = config('installer.store.project_path');
        $file = $projectPath.'/'.ltrim($file, '/');

        return file_put_contents(
            $file,
            str_replace($search, $replace, file_get_contents($file))
        );
    }

    protected function replaceInStub(array $replacements, string $replace, string $stub)
    {
        $rootPath = config('installer.store.root_path');
        $contents = File::get(config('installer.store.root_path').'/'.ltrim($stub, '/'));

        foreach ($replacements as $search => $replace) {
            str_replace($search, $replace, $contents);
        }

        return file_put_contents(
            config('installer.store.root_path').'/'.ltrim($stub, '/'),
            $contents
        );
    }

    protected function insertInProjectFile(string $file, array $lines = [], bool $newlineBefore = true, bool $newlineAfter = true): bool
    {
        $projectPath = config('installer.store.project_path');
        $file = $projectPath.'/'.ltrim($file, '/');

        $contents = File::get($file);
        $parsedByLine = collect(explode("\n", $contents))->toArray();

        $position = array_search("    Route::name('welcome')->get('/', [PageController::class, 'index']);", $parsedByLine);

        $start = array_slice($parsedByLine, 0, $position + 1);
        $end = array_slice($parsedByLine, $position + 1);

        $newArray = array_merge(
            $start,
            $newlineBefore ? [''] : [],
            $lines,
            $newlineAfter ? [''] : [],
            $end,
        );

        return File::put($file, implode("\n", $newArray));
    }

    protected function deleteInProjectFile(string $file, array $lines = []): bool
    {
        $projectPath = config('installer.store.project_path');
        $file = $projectPath.'/'.ltrim($file, '/');

        $contents = File::get($file);
        $parsed = collect(explode("\n", $contents))->filter(function ($line) use ($lines) {
            return ! in_array($line, $lines);
        })->implode("\n");

        return File::put($file, $parsed);
    }
}
