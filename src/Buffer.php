<?php

declare(strict_types=1);

namespace Devlop\Buffer;

use InvalidArgumentException;

final class Buffer
{
    /**
     * @var array<int,mixed>
     */
    private array $stack = [];

    private int $size;

    /**
     * @var callable
     */
    private $callback;

    /**
     * Create a new buffer.
     *
     * @param  int  $size
     * @param  callable  $callback
     * @return void
     */
    public function __construct(int $size, callable $callback)
    {
        if ($size <= 0) {
            throw new InvalidArgumentException('Argument $size must be positive.');
        }

        $this->callback = $callback;

        $this->size = $size;
    }

    /**
     * Iterate over an iterable and automatically push to
     * the buffer until everything have been iterated
     *
     * @param  iterable  $iterable
     * @param  int  $size
     * @param  callable  $callback
     * @return void
     */
    public static function iterate(iterable $iterable, int $size, callable $callback) : void
    {
        $buffer = new static($size, $callback);

        foreach ($iterable as $value) {
            $buffer->push($value);
        }

        $buffer->flush();
    }

    /**
     * Push an item onto the stack
     *
     * @param  mixed  $item
     * @return void
     */
    public function push($item) : void
    {
        $this->stack[] = $item;

        if (count($this->stack) >= $this->size) {
            $this->flush();
        }
    }

    /**
     * Flush the stack to the callback and erase the stack after invoking the callback
     *
     * @return void
     */
    public function flush() : void
    {
        if (count($this->stack) > 0) {
            call_user_func($this->callback, $this->stack);
        }

        $this->clean();
    }

    /**
     * Clean (erase) the stack.
     *
     * @return void
     */
    private function clean() : void
    {
        $this->stack = [];
    }
}
