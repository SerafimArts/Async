<?php

/**
 * This file is part of Async package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Async;

/**
 * Interface LoopInterface
 */
interface LoopInterface
{
    /**
     * @return void
     */
    public function run(): void;
}
