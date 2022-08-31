<?php

namespace Tests\Feature;

use App\Actions\ValetSecure;
use App\LamboException;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class ValetSecureTest extends TestCase
{
    /** @test */
    public function it_runs_valet_secure()
    {
        config(['lambo.store.valet_secure' => true]);

        $this->shell->shouldReceive('execInProject')
            ->with('valet secure')
            ->once()
            ->andReturn(FakeProcess::success());

        app(ValetSecure::class)();
    }

    /** @test */
    public function it_throws_an_exception_if_valet_secure_fails()
    {
        config(['lambo.store.valet_secure' => true]);

        $this->shell->shouldReceive('execInProject')
            ->with('valet secure')
            ->once()
            ->andReturn(FakeProcess::fail('valet secure'));

        $this->expectException(LamboException::class);

        app(ValetSecure::class)();
    }
}
