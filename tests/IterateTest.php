<?php

declare(strict_types=1);

namespace Devlop\Buffer\Tests;

use Devlop\Buffer\Buffer;
use PHPUnit\Framework\TestCase;

final class IterateTest extends TestCase
{
    /** @test */
    public function iterate_method_should_iterate_over_the_input_and_invoke_the_callback_everytime_size_is_reached_and_when_done_iterating() : void
    {
        $items = [
            'value1',
            'value2', // callback should be called here
            'value3', // and also here
        ];

        $callbackCalledTimes = 0;

        Buffer::iterate($items, 2, function ($items) use (&$callbackCalledTimes) {
            $callbackCalledTimes++;
        });

        $this->assertEquals(2, $callbackCalledTimes);
    }
}
