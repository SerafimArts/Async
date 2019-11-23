<?php

/**
 * This file is part of Async package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Serafim\Async\EventLoop;
use React\Promise\PromiseInterface;

/**
 * @param Closure $expr
 * @return PromiseInterface
 */
function async(\Closure $expr): PromiseInterface
{
    $instance = EventLoop::getInstance();
    $instance->deferred();

    return $instance->async($expr);
}
