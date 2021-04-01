<?php

declare(strict_types=1);

namespace Devlop\Buffer\Tests;

use Countable;
use Devlop\Buffer\Buffer;
use Devlop\PHPUnit\ExceptionAssertions;
use PHPUnit\Framework\TestCase;

final class BufferTest extends TestCase
{
    use ExceptionAssertions;

    /** @test */
    public function it_cannot_be_instantiated_with_a_non_positive_size() : void
    {
        $sizes = [
            0,
            -1,
        ];

        foreach ($sizes as $size) {
            $this->assertExceptionThrown(\InvalidArgumentException::class, function () use ($size) : void {
                new Buffer($size, function (array $items) {
                    //
                });
            });
        }
    }

    /** @test */
    public function it_can_be_instantiated_with_positive_size() : void
    {
        $sizes = [
            1,
            10,
        ];

        foreach ($sizes as $size) {
            $this->assertNoExceptionsThrown(function () use ($size) : void {
                new Buffer($size, function (array $items) {
                    //
                });
            });
        }
    }

    /** @test */
    public function the_buffer_is_countable() : void
    {
        $buffer = new Buffer(1, function (array $items) {
            //
        });

        $this->assertInstanceOf(Countable::class, $buffer);
    }

    /** @test */
    public function push_increases_the_count_of_the_buffer() : void
    {
        $buffer = new Buffer(10, function (array $items) {
            //
        });

        $previousCount = count($buffer);

        $buffer->push('value');

        $this->assertEquals(
            $previousCount + 1,
            count($buffer),
        );
    }

    /** @test */
    public function is_empty_returns_true_on_empty_buffer() : void
    {
        $buffer = new Buffer(10, function (array $items) {
            //
        });

        $this->assertTrue($buffer->isEmpty());
    }

    /** @test */
    public function is_empty_returns_false_on_non_empty_buffer() : void
    {
        $buffer = new Buffer(10, function (array $items) {
            //
        });

        $buffer->push('value');

        $this->assertFalse($buffer->isEmpty());
    }

    /** @test */
    public function clean_empties_the_buffer() : void
    {
        $buffer = new Buffer(10, function (array $items) {
            //
        });

        $buffer->push('value');

        $this->assertFalse($buffer->isEmpty());

        $buffer->clean();

        $this->assertTrue($buffer->isEmpty());
    }

    /** @test */
    public function the_buffer_is_flushed_when_the_size_is_reached()
    {
        $callbackCalled = false;

        $buffer = new Buffer(2, function (array $items) use (&$callbackCalled) {
            $callbackCalled = true;
        });

        $buffer->push('value');
        $buffer->push('value'); // callback should be called now

        $this->assertTrue($callbackCalled);
    }

    /** @test */
    public function the_buffer_should_be_empty_after_the_buffer_has_been_flushed()
    {
        $callbackCalled = false;

        $buffer = new Buffer(2, function (array $items) use (&$callbackCalled) {
            $callbackCalled = true;
        });

        $buffer->push('value');
        $buffer->push('value'); // callback should be called now

        $this->assertTrue($buffer->isEmpty());
    }

    /** @test */
    public function the_callback_should_receive_the_same_values_as_put_into_the_stack()
    {
        $containsAllValues = false;

        $buffer = new Buffer(2, function (array $items) use (&$containsAllValues) {
            $expectedValues = [
                'value1',
                'value2',
            ];

            foreach($expectedValues as $expectedValue) {
                if (! in_array($expectedValue, $items, true)) {
                    return;
                }
            }

            $containsAllValues = true;
        });

        $buffer->push('value1');
        $buffer->push('value2'); // callback should be called now

        $this->assertTrue($containsAllValues);
    }

    /** @test */
    public function the_callback_should_receive_same_count_of_values_as_put_into_the_stack()
    {
        $containsAllValues = false;

        $buffer = new Buffer(2, function (array $items) use (&$containsAllValues) {
            $containsAllValues = count($items) === 2;
        });

        $buffer->push('value1');
        $buffer->push('value2'); // callback should be called now

        $this->assertTrue($containsAllValues);
    }

    /** @test */
    public function the_callback_should_receive_remaining_items_when_flushed()
    {
        $containsAllValues = false;

        $buffer = new Buffer(10, function (array $items) use (&$containsAllValues) {
            $containsAllValues = count($items) === 2;
        });

        $buffer->push('value1');
        $buffer->push('value2');

        $buffer->flush();

        $this->assertTrue($containsAllValues);
    }
}
