<?php

namespace Rizzello\LaravelPackageSystem\Support;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Rizzello\LaravelPackageSystem\Exceptions\PackageConfigurationException;

/**
 * Class AbstractPackageServiceProvider
 *
 * @package Rizzello\LaravelPackageSystem\Support
 */
abstract class AbstractPackageServiceProvider extends ServiceProvider
{
    /**
     * The package default options.
     *
     * @var array
     */
    private $defaultOptions = [
        'package_name' => null,
        'migrations' => true,
        'routes_web' => true,
        'routes_api' => true,
        'views' => true,
        'views_name' => null,
    ];

    /**
     * The package options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The package base path.
     *
     * @var string|null
     */
    protected $packageBasePath = null;

    /**
     * The configuration files that should be loaded.
     *
     * @var string[]
     */
    protected $configs = [];

    /**
     * The aliases that should be loaded.
     *
     * @var string[]
     */
    protected $aliases = [];

    /**
     * The event handlers mapping.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * The translations that should be registered.
     *
     * @var string[]
     */
    protected $translations = [];

    /**
     * The commands that should be registered.
     *
     * @var string[]
     */
    protected $commands = [];

    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct($app);

        if ($this->packageBasePath === null) {
            $this->packageBasePath = $this->guessPackageBasePath();
        }
    }

    /**
     * Boot services.
     *
     * @throws PackageConfigurationException
     */
    public function boot()
    {
        $options = $this->getPackageOptions();

        if ($options['migrations']) {
            $this->loadMigrationsFrom(
                "{$this->packageBasePath}/database/migrations"
            );
        }

        if ($options['routes_web']) {
            $this->loadRoutesFrom("{$this->packageBasePath}/routes/web.php");
        }

        if ($options['routes_api']) {
            $this->registerApiRoutes();
        }

        if ($options['views']) {
            /*
             * Default package view namespace registration
             */
            $viewNamespace = $options['views_name'] ?? $options['package_name'];
            $this->registerViewNamespace(
                "{$this->packageBasePath}/resources/views",
                $viewNamespace
            );
        }

        /*
         * Event listeners registration
         */
        foreach ($this->listen as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                Event::listen($event, $listener);
            }
        }

        /*
         * Translation files registration
         */
        foreach ($this->translations as $translation) {
            $this->registerTranslation(
                "{$this->packageBasePath}/resources/lang",
                $translation
            );
        }

        /*
         * Console commands registration
         */
        if ($this->app->runningInConsole() && !empty($this->commands)) {
            $this->commands($this->commands);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (empty($this->options['package_name'] ?? null)) {
            throw new PackageConfigurationException(
                'Name of the package must be set'
            );
        }

        foreach ($this->configs as $config) {
            $this->registerConfig("{$this->packageBasePath}/config", $config);
        }

        foreach ($this->aliases as $alias => $class) {
            $this->registerAlias($alias, $class);
        }
    }

    /**
     * Guess the package base path using composer.
     *
     * @return string
     */
    protected function guessPackageBasePath(): string
    {
        $autoloader = require base_path('/vendor/autoload.php');
        $file = $autoloader->findFile(static::class);
        return dirname($file) . '/..';
    }

    /**
     * Returns the package options array.
     *
     * @return array
     */
    protected function getPackageOptions(): array
    {
        return array_merge($this->defaultOptions, $this->options);
    }

    /**
     * Register api routes.
     */
    private function registerApiRoutes()
    {
        Route::group(
            [
                'prefix' => 'api',
            ],
            function ($router) {
                require "{$this->packageBasePath}/routes/api.php";
            }
        );
    }

    /**
     * Add an alias to the loader.
     * Should be called in register() method.
     *
     * @param  string  $class
     * @param  string  $alias
     * @return void
     */
    protected function registerAlias($class, $alias)
    {
        AliasLoader::getInstance()->alias($class, $alias);
    }

    /**
     * Merge the given configuration with the existing configuration.
     * Register the config file to be published by the publish command if $publish = true.
     * Should be called in register() method.
     *
     * @param string $configFolder
     * @param string $configName
     * @param bool $publish
     */
    protected function registerConfig(
        string $configFolder,
        string $configName,
        bool $publish = true
    ) {
        $configFullPath = "{$configFolder}/{$configName}.php";

        $this->mergeConfigFrom($configFullPath, $configName);

        if ($publish) {
            $this->publishes(
                [
                    $configFullPath => config_path($configName . '.php'),
                ],
                "{$this->options['package_name']}-configs"
            );
        }
    }

    /**
     * Register a translation file namespace.
     * Register the lang folder to be published by the publish command if $publish = true.
     * Should be called in boot() method.
     *
     * @param string $langFolder
     * @param string $langNamespace
     * @param bool $publish
     */
    protected function registerTranslation(
        string $langFolder,
        string $langNamespace,
        bool $publish = true
    ) {
        $this->loadTranslationsFrom($langFolder, $langNamespace);

        if ($publish) {
            $this->publishes(
                [
                    $langFolder => resource_path(
                        "lang/vendor/{$this->options['package_name']}"
                    ),
                ],
                "{$this->options['package_name']}-lang"
            );
        }
    }

    /**
     * Register a view files namespace.
     * Register the view folder to be published by the publish command if $publish = true.
     * Should be called in boot() method.
     *
     * @param string $viewFolder
     * @param string $viewNamespace
     * @param bool $publish
     */
    protected function registerViewNamespace(
        string $viewFolder,
        string $viewNamespace,
        bool $publish = true
    ) {
        $this->loadViewsFrom($viewFolder, $viewNamespace);

        if ($publish) {
            $this->publishes(
                [
                    $viewFolder => resource_path(
                        "views/vendor/{$viewNamespace}"
                    ),
                ],
                "{$this->options['package_name']}-views"
            );
        }
    }
}
