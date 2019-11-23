<?php

/**
 * This file is part of Async package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Async;

use React\Promise\Deferred;
use React\Promise\PromiseInterface;

/**
 * Class EventLoop
 */
final class EventLoop implements LoopInterface
{
    /**
     * @var self|null
     */
    private static $instance;

    /**
     * @var bool
     */
    private $running = false;

    /**
     * @var \SplObjectStorage|PromiseInterface[]|Process[]
     */
    private $pool;

    /**
     * @var \SplObjectStorage|PromiseInterface[]
     */
    private $awaited;

    /**
     * @var array|\Closure[]
     */
    private $tickHandlers = [];

    /**
     * EventLoop constructor.
     */
    private function __construct()
    {
        $this->pool = new \SplObjectStorage();
        $this->awaited = new \SplObjectStorage();
    }

    /**
     * @param \Closure $expr
     * @return void
     */
    public function onTick(\Closure $expr): void
    {
        $this->tickHandlers[] = $expr;
    }

    /**
     * @param \Closure $expr
     * @return PromiseInterface
     */
    public function async(\Closure $expr): PromiseInterface
    {
        $deferred = new Deferred();
        $promise = $deferred->promise();

        $this->pool->attach($promise, new Process($expr, $deferred));

        return $promise;
    }

    /**
     * @return PromiseInterface
     */
    public static function boot(): PromiseInterface
    {
        return static::getInstance()->deferred();
    }

    /**
     * @return PromiseInterface
     */
    public function deferred(): PromiseInterface
    {
        $deferred = new Deferred();

        if ($this->running) {
            $deferred->resolve($this);
        } else {
            \register_shutdown_function(function () use ($deferred) {
                $deferred->resolve($this);

                $this->run();
            });
        }

        return $deferred->promise();
    }

    /**
     * @return void
     */
    public function run(): void
    {
        if ($this->running) {
            return;
        }

        $this->running = true;

        while ($this->pool->count()) {
            foreach ($this->pool as $id => $promise) {
                $this->tick($promise, $this->pool[$promise]);

                foreach ($this->tickHandlers as $handler) {
                    $handler();
                }
            }
        }
    }

    /**
     * @param PromiseInterface $promise
     * @param Process $process
     * @return void
     */
    private function tick(PromiseInterface $promise, Process $process): void
    {
        try {
            switch (true) {
                case ! $process->isBooted():
                    $process->boot();

                    return;

                case ! $process->value instanceof \Generator:
                    $this->complete($promise, $process->value);

                    return;

                case ! $process->value->valid():
                    $process->value = $process->value->getReturn();

                    return;

                default:
                    $this->tickCoroutine($process->value);
            }
        } catch (\Throwable $e) {
            $process->deferred->reject($e);

            $this->cancel($promise);
        }
    }

    /**
     * @param PromiseInterface $promise
     * @param mixed $value
     * @return void
     */
    private function complete(PromiseInterface $promise, $value): void
    {
        $process = $this->pool[$promise];

        $process->value = $value;
        $process->deferred->resolve($value);

        unset($this->pool[$promise]);
    }

    /**
     * @param \Generator $generator
     * @return void
     */
    private function tickCoroutine(\Generator $generator): void
    {
        $current = $generator->current();

        if (! $current instanceof PromiseInterface) {
            $generator->send($current);

            return;
        }

        $this->waitPromiseThenSend($generator, $current);
    }

    /**
     * @param \Generator $generator
     * @param PromiseInterface $promise
     * @return void
     */
    private function waitPromiseThenSend(\Generator $generator, PromiseInterface $promise): void
    {
        if (! isset($this->awaited[$promise])) {
            $this->awaited->attach($promise);

            $fulfilled = static function ($value) use ($promise, $generator) {
                $generator->send($value);
                unset($this->awaited[$promise]);
            };

            $rejected = static function (\Throwable $e) use ($promise, $generator) {
                $generator->throw($e);
                unset($this->awaited[$promise]);
            };

            $promise->then($fulfilled, $rejected);
        }
    }

    /**
     * @param PromiseInterface $promise
     * @return void
     */
    public function cancel(PromiseInterface $promise): void
    {
        $this->assertPromise($promise);

        unset($this->pool[$promise]);
    }

    /**
     * @param PromiseInterface $promise
     * @return void
     */
    private function assertPromise(PromiseInterface $promise): void
    {
        if (! isset($this->pool[$promise])) {
            throw new \InvalidArgumentException('This Promise is not part of the EventLoop');
        }
    }

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        return self::$instance ?? self::$instance = new static();
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->running;
    }
}
