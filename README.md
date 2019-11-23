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

## All you need to know

1) Function `async` returns a Promise.
2) Result of a promise is an `return` value of given closure.
3) Every `yield` expression is an EventLoop "tick".
4) Every `yield Promise` or `return Promise` waits for completion, and then returns the result.

## Examples

```php
$promise = async(fn () => 23)
$promise->then(fn ($value) => echo $value)

// 23
```

----

```php
$promise = async(function () {
    echo yield 1;
    echo yield 2;
    
    return 3;
});
$promise->then(fn ($value) => echo $value)

// 123
```

----

```php
async(function () {
    echo yield 1;
    echo yield 3;
});

async(function () {
    echo yield 2;
    echo yield 4;
});

// 1234
```

----

```php
async(function() {
    $promise = async(fn () => 23);
    
    echo yield $promise; // 23
});
```

----

```php
$promise = async(function() {
    return async(fn () => 23);
});

$promise->then(fn ($value) => echo $value);
// 23
```

TL;DR; Do not us it =)

## License

See [LICENSE](https://github.com/SerafimArts/Async/master/LICENSE.md)
