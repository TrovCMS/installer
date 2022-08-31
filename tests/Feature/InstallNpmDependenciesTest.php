<?php

namespace Tests\Feature;

use App\Actions\InstallNpmDependencies;
use App\LamboException;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class InstallNpmDependenciesTest extends TestCase
{
    /** @test */
    public function it_installs_npm_dependencies()
    {
        config(['lambo.store.with_output' => false]);

        $this->shell->shouldReceive('execInProject')
            ->with('npm install --silent')
            ->once()
            ->andReturn(FakeProcess::success());

        app(InstallNpmDependencies::class)();
    }

    /** @test */
    public function it_installs_npm_dependencies_and_shows_console_output()
    {
        config(['lambo.store.with_output' => true]);

        $this->shell->shouldReceive('execInProject')
            ->with('npm install')
            ->once()
            ->andReturn(FakeProcess::success());

        app(InstallNpmDependencies::class)();
    }

    /** @test */
    public function it_throws_an_exception_if_npm_install_fails()
    {
        config(['lambo.store.with_output' => false]);

        $this->shell->shouldReceive('execInProject')
            ->with('npm install --silent')
            ->once()
            ->andReturn(FakeProcess::fail('npm install --silent'));

        $this->expectException(LamboException::class);

        app(InstallNpmDependencies::class)();
    }
}
