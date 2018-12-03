<?php
namespace Tests;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $app;

    protected function setUp()
    {
        parent::setUp();

        $this->app = Mockery::mock(Application::class)->makePartial();

        $this->app->alias(Repository::class, 'config');
        $this->app->instance('config', new Repository([]));

        $this->app->alias(Request::class, 'request');
        $this->app->instance('request', new Request());

        Container::setInstance($this->app);
    }
}
