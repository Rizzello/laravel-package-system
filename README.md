# Laravel Package System

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Description

A basic package system for Laravel 6.0 and up.

This package allows you to easily create packages.

## Install

```
composer require rizzello/laravel-package-system
```

## Usage

You can generate your own package using the following artisan commad:

```
php artisan make:package <vendor> <name>
```

Command options:

-   --migrations - The package has migrations
-   --web - The package has web routes
-   --api - The package has api routes
-   --views - The package has views

Additional customizations:

-   --namespace=&lt;package namespace&gt; - The namespace ot the package
-   --path=&lt;path&gt; - The path where the package should be generated

### Package folder structure

```
package-name
┣ config/
┣ resources/
┃ ┣ lang/
┃ ┗ views/
┣ src/
┃ ┣ ...
┃ ┗ PackageNameServiceProvider.php
┣ composer.json
┗ README.md
```

## Test

```
composer test
```
