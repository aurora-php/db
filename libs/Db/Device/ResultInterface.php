<?php

declare(strict_types=1);

/*
 * This file is part of the 'octris/db' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Db\Device;

/**
 * Interface for database query results.
 *
 * @copyright   copyright (c) 2016-present by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
interface ResultInterface extends \Iterator, \Countable
{
}
