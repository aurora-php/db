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
 * Device types.
 *
 * @copyright   copyright (c) 2021-present by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
enum Type: string {
    case MASTER = 'master';
    case SLAVE = 'slave';

    public function isMaster(): bool
    {
        return ($this == self::MASTER);
    }
}
