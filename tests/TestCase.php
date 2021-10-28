<?php

namespace Rizzello\LaravelPackageSystem\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Rizzello\LaravelPackageSystem\PackageSystemServiceProvider;

/**
 * Class TestCase
 *
 * @package Rizzello\LaravelPackageSystem\Tests
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [PackageSystemServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
