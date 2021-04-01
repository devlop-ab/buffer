<?php

declare(strict_types=1);

namespace Devlop\Buffer\Tests;

use Devlop\Buffer\Buffer;
use Generator;
use PHPUnit\Framework\TestCase;

final class YieldTest extends TestCase
{
    /** @test */
    public function yield_method_should_return_generator() : void
    {
        $this->assertInstanceOf(
            Generator::class,
            Buffer::yield([], 2, fn () => null),
        );
    }

    /** @test */
    public function yield_method_should_yield_each_input_value() : void
    {
        $items = [
            'value1',
            'value2',
            'value3',
        ];

        $callback = function ($items) : Generator {
            foreach ($items as $item) {
                yield $item;
            }
        };

        $index = 0;

        $yieldedValues = 0;

        foreach (Buffer::yield($items, 2, $callback) as $item) {
            $this->assertEquals(
                $items[$index++],
                $item,
            );

            $yieldedValues++;
        }

        $this->assertGreaterThan(0, $yieldedValues, 'No values was yielded');
    }
}
