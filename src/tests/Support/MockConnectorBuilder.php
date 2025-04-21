<?php

declare(strict_types=1);

namespace Tests\Support;

use Closure;
use Illuminate\Foundation\Application;
use Mockery;
use Mockery\MockInterface;
use Tests\Support\WPConnector\FakeQuery;
use WPAjaxConnector\WPAjaxConnectorPHP\WPConnectorInterface;

class MockConnectorBuilder
{
    private array $methods = [];

    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function mockAddPost(Closure $callable): self
    {
        $this->methods['addPost'] = $callable;

        return $this;
    }

    public function mockGetPost(Closure $callable): self
    {
        $this->methods['getPost'] = $callable;

        return $this;
    }

    public function mockGetPostThumbnail(Closure $callable): self
    {
        $this->methods['getPostThumbnail'] = $callable;

        return $this;
    }

    public function mockGetPostBlocks(Closure $callable): self
    {
        $this->methods['getPostBlocks'] = $callable;

        return $this;
    }

    public function mockSetPostBlocks(Closure $callable): self
    {
        $this->methods['setPostBlocks'] = $callable;

        return $this;
    }

    public function mockSetPostThumbnail(Closure $callable): self
    {
        $this->methods['setPostThumbnail'] = $callable;

        return $this;
    }

    public function mockSetPostTitle(Closure $callable): self
    {
        $this->methods['setPostTitle'] = $callable;

        return $this;
    }

    public function mockSetPostCategory(Closure $callable): self
    {
        $this->methods['setPostCategory'] = $callable;

        return $this;
    }

    public function mockAddAttachment(Closure $callable): self
    {
        $this->methods['addAttachment'] = $callable;

        return $this;
    }

    public function build(): WPCOnnectorInterface
    {
        $mock = Mockery::mock(WPConnectorInterface::class, function (MockInterface $mock) {
            foreach ($this->methods as $method => $callback) {
                $mock->shouldReceive($method)->andReturnUsing($callback);
            }

            $mock->shouldReceive('query')->andReturnUsing(function () {
                return new FakeQuery;
            });
        });

        return $this->app->instance(WPConnectorInterface::class, $mock);
    }
}
