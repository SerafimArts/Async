# Async

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
