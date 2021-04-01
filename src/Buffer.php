<?php

declare(strict_types=1);

namespace Devlop\Buffer;

use Countable;
use Generator;
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
     * Iterate over an iterable and allow yielding of
     * individual items from inside the callback
     *
     * @param  iterable<mixed>  $iterable
     * @param  int  $size
     * @param  callable  $callback
     * @return Generator
     */
    public static function yield(iterable $iterable, int $size, callable $callback) : Generator
    {
        $stack = [];

        /**
         * Create the buffer with a "wrapper" callback that catches the yields emitted
         * by the callback and puts them into a seperate $stack variable.
         */
        $buffer = new static($size, function (array $items) use ($callback, &$stack) : void {
            foreach (call_user_func($callback, $items) as $item) {
                $stack[] = $item;
            }
        });

        foreach ($iterable as $value) {
            $buffer->push($value);

            /**
             * Yield everything in the stack (if the callback was applied).
             */
            while (count($stack) > 0) {
                yield array_shift($stack);
            }
        }

        $buffer->flush();

        /**
         * And lastly we have to yield everything remaining in the stack.
         */
        while (count($stack) > 0) {
            yield array_shift($stack);
        }
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
     * Apply the callback to the stack and then erase the stack.
     */
    public function flush() : void
    {
        if (! $this->isEmpty()) {
            call_user_func($this->callback, $this->stack);
        }

        $this->clean();
    }

    /**
     * Erase the stack without applying the callback.
     */
    public function clean() : void
    {
        $this->stack = [];
    }

    /**
     * If the buffer is empty.
     */
    public function isEmpty() : bool
    {
        return ! $this->isNotEmpty();
    }

    /**
     * If the buffer is not empty.
     */
    public function isNotEmpty() : bool
    {
        return count($this) > 0;
    }

    /**
     * The current size of the buffer.
     */
    public function count() : int
    {
        return count($this->stack);
    }
}
