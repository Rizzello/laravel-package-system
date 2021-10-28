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
     * @var string[]
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

        if (empty($options['package_name'] ?? null)) {
            throw new PackageConfigurationException(
                'Name of the package must be set'
            );
        }

        if ($options['migrations']) {
            $this->loadMigrationsFrom(
                $this->packageBasePath . '/database/migrations'
            );
        }

        if ($options['routes_web']) {
            $this->loadRoutesFrom($this->packageBasePath . '/routes/web.php');
        }

        if ($options['routes_api']) {
            $this->registerApiRoutes();
        }

        if ($options['views']) {
            $this->registerViews(
                $options['views_name'] ?? $options['package_name']
            );
        }

        $this->registerEventListeners();

        $this->registerTranslations();

        $this->registerCommands();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->aliases as $alias => $class) {
            $this->registerAlias($alias, $class);
        }
    }

    /**
     * Guess the package base path using composer.
     *
     * @return string
     */
    protected function guessPackageBasePath()
    {
        $autoloader = require base_path('/vendor/autoload.php');
        $file = $autoloader->findFile(static::class);
        return dirname($file) . '/..';
    }

    /**
     * Add an alias to the loader.
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
    public function registerApiRoutes()
    {
        Route::group(
            [
                'prefix' => 'api',
            ],
            function ($router) {
                require $this->packageBasePath . '/routes/api.php';
            }
        );
    }

    /**
     * Register all views.
     *
     * @param string $namespace
     */
    public function registerViews(string $namespace)
    {
        $this->loadViewsFrom(
            $this->packageBasePath . '/resources/views',
            $namespace
        );

        $this->publishes(
            [
                $this->packageBasePath . '/resources/views' => resource_path(
                    'views/vendor/' . $namespace
                ),
            ],
            'views'
        );
    }

    /**
     * Register all event listeners.
     */
    public function registerEventListeners()
    {
        foreach ($this->listen as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                Event::listen($event, $listener);
            }
        }
    }

    /**
     * Load all translation files.
     */
    public function registerTranslations()
    {
        foreach ($this->translations as $translation) {
            $this->loadTranslationsFrom(
                $this->packageBasePath . '/resources/languages',
                $translation
            );
        }
    }

    /**
     * Register all console commands.
     */
    public function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            foreach ($this->commands as $command) {
                $this->commands([$command]);
            }
        }
    }
}
