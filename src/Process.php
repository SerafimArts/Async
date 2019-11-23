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

/**
 * Class Process
 */
class Process
{
    /**
     * @var bool
     */
    private $booted = false;

    /**
     * @var mixed|\Generator|\Closure
     */
    public $value;

    /**
     * @var Deferred
     */
    public $deferred;

    /**
     * Process constructor.
     *
     * @param \Closure $expr
     * @param Deferred $deferred
     */
    public function __construct(\Closure $expr, Deferred $deferred)
    {
        $this->value = $expr;
        $this->deferred = $deferred;
    }

    /**
     * @return bool
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->booted = true;
        $this->value = ($this->value)();
    }
}
