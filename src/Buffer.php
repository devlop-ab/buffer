<?php

declare(strict_types=1);

namespace Devlop\Buffer;

use Countable;
use InvalidArgumentException;

final class Buffer implements Countable
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
            throw new InvalidArgumentException('Argument $size must be a positive integer.');
        }

        $this->callback = $callback;

        $this->size = $size;
    }

    /**
     * Iterate over an iterable and automatically push to
     * the buffer until everything have been iterated
     *
     * @param  iterable<mixed>  $iterable
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

        if (count($this) >= $this->size) {
            $this->flush();
        }
    }

    /**
     * Flush the stack to the callback and erase the stack after invoking the callback
     */
    public function flush() : void
    {
        if (! $this->isEmpty()) {
            call_user_func($this->callback, $this->stack);
        }

        $this->clean();
    }

    /**
     * Clean (erase) the stack.
     */
    public function clean() : void
    {
        $this->stack = [];
    }

    /**
     * If the buffer is empty
     */
    public function isEmpty() : bool
    {
        return count($this) === 0;
    }

    /**
     * The current size of the buffer
     */
    public function count() : int
    {
        return count($this->stack);
    }
}
