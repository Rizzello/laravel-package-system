<?php

namespace Rizzello\LaravelPackageSystem\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Class MakePackageCommand
 *
 * @package Rizzello\LaravelPackageSystem\Console\Commands
 */
class MakePackageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:package
        {vendor : The package vendor (in kebab case)}
        {name : The package name (in kebab case)}
        {--namespace= : The namespace of the package (default is "VendorName\PackageName")}
        {--path= : The path of the package (default is "packages/vendor-name/package-name/")}
        {--migrations : The package has migrations}
        {--web : The package has web routes}
        {--api : The package has api routes}
        {--views : The package has views}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new laravel package';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /**
         * Package vendor name (in kebab case)
         * @var string
         */
        $packageVendor = $this->input->getArgument('vendor');

        /**
         * Package name (in kebab case)
         * @var string
         */
        $packageName = $this->input->getArgument('name');

        /**
         * Package namespace
         * @var string
         */
        $namespace =
            Str::studly($packageVendor) . '\\' . Str::studly($packageName);

        /**
         * Package path location
         * @var string
         */
        $path =
            $this->input->getOption('path') ?:
            base_path("packages/{$packageVendor}/{name}");
        rtrim($path, '/');

        /*
         * Abort if path is already taken
         */
        if (file_exists($path)) {
            $this->error('Cannot create directory: File exists!');
            return;
        }

        /*
         * Flags
         */
        $hasMigrations = $this->option('migrations');
        $hasWebRoutes = $this->option('web');
        $hasApiRoutes = $this->option('api');
        $hasViews = $this->option('views');
    }
}
