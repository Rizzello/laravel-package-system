<?php

namespace Rizzello\LaravelPackageSystem;

use Rizzello\LaravelPackageSystem\Support\AbstractPackageServiceProvider;

/**
 * Class PackageSystemServiceProvider
 *
 * @package Rizzello\LaravelPackageSystem
 */
class PackageSystemServiceProvider extends AbstractPackageServiceProvider
{
    /**
     * The package options.
     *
     * @var array
     */
    protected $options = [
        'package_name' => 'package-system',
        'migrations' => false,
        'routes_web' => false,
        'routes_api' => false,
        'views' => false,
    ];

    /**
     * The package base path.
     *
     * @var string
     */
    protected $packageBasePath = __DIR__ . '/..';

    /**
     * The configuration files that should be loaded.
     *
     * @var string[]
     */
    protected $configs = ['package-system'];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        /*
         * Parent register() to register functionalities declared in class
         */
        parent::register();

        /*
         * Packages registration from config file
         */
        $packages = config('package-system.packages', []);
        foreach ($packages as $package) {
            $this->app->register($package);
        }
    }
}
