<p align="center">
    <a href="https://packagist.org/packages/devlop/buffer"><img src="https://img.shields.io/packagist/v/devlop/buffer" alt="Latest Stable Version"></a>
    <a href="https://github.com/devlop-ab/buffer/blob/master/LICENSE.md"><img src="https://img.shields.io/packagist/l/devlop/buffer" alt="License"></a>
</p>

# Buffer

Simple Buffer for iterating over an iteratable and regularly applying a callback.

This allows you to consume a large array with minimal memory usage.

# Installation

```bash
composer require devlop/buffer
```

# Usage

## Manual

This way gives you the most power, but also forces you to take more responsibility over execution.

```php
use Devlop\Buffer\Buffer;

$bigFuckingArray = [...]; // array containing between zero and many many items

$buffer = new Buffer(
    10, // max Buffer size
    function (array $items) : void {
        // callback to apply when buffer size reaches max
    },
);

foreach ($bigFuckingArray as $key => $value) {
    $buffer->push($value); // the Buffer callback will automatically be applied when needed
}

// important, after looping over the array, remember to manually call the flush() method to apply the callback on last time if needed
$buffer->flush();
```

## Automatic

This way is the easiest way to use the Buffer and requires least work from you.

```php
use Devlop\Buffer\Buffer;

$bigFuckingArray = [...]; // array containing between zero and many many items

Buffer::iterate(
    $bigFuckingArray, // input iterable
    10, // max Buffer size
    function (array $items) : void {
        // callback to apply when buffer size reaches max
        // the callback will also be called one last time after finishing iterating if needed
    },
)
```
