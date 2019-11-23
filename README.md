# Async

<p align="center">
    <a href="https://travis-ci.org/SerafimArts/Async"><img src="https://travis-ci.org/SerafimArts/Async.svg?branch=master" alt="Build Status"></a>
    <a href="https://codeclimate.com/github/SerafimArts/Async/maintainability"><img src="https://api.codeclimate.com/v1/badges/b2606e4aa0d70307198d/maintainability" /></a>
    <a href="https://codeclimate.com/github/SerafimArts/Async/test_coverage"><img src="https://api.codeclimate.com/v1/badges/b2606e4aa0d70307198d/test_coverage" /></a>
</p>
<p align="center">
    <a href="https://packagist.org/packages/serafim/async"><img src="https://img.shields.io/badge/PHP-7.2+-4f5b93.svg" alt="PHP 7.1+"></a>
    <a href="https://packagist.org/packages/serafim/async"><img src="https://poser.pugx.org/serafim/async/version" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/serafim/async"><img src="https://poser.pugx.org/serafim/async/downloads" alt="Total Downloads"></a>
    <a href="https://raw.githubusercontent.com/SerafimArts/Async/master/LICENSE.md"><img src="https://poser.pugx.org/serafim/async/license" alt="License MIT"></a>
</p>

## Installation

Install via [Composer](https://getcomposer.org/):

```sh
composer require serafim/async
```

## Usage

Async "Hello World". Just for fun %)

```php
$fn = async(static function () {
    echo yield 1;

    return async(static function () {
        echo yield 2;
        echo yield 3;

        return 'Word';
    });
});

$prom = async(static function () use ($fn) {
    echo yield 4;

    return $fn;
})->then(static function ($value) {
    $promise = async(static function () use ($value) {
        echo yield 5;
        echo yield 6;

        return $value;
    });

    $promise->then(static function ($value) {
        echo "\nHell Or $value\n";
    });
});
```

TL;DR; Do not us it =)

## License

See [LICENSE](https://github.com/SerafimArts/Async/master/LICENSE.md)
