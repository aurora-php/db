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
 * Interface for database connection.
 *
 * @copyright   copyright (c) 2012-present by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
interface ConnectionInterface
{
    /**
     * Whether pooling is enabled for connection.
     *
     * @return bool
     */
    public function doPooling(): bool;

    /**
     * Close connection.
     */
    public function close(): void;

    /**
     * Release connection.
     */
    public function release(): void;

    /**
     * Check availability of a connection.
     */
    public function isAlive(): bool;

    /**
     * Resolve a database reference.
     *
     * @param   \Octris\Db\Type\DbRef                          $dbref       Database reference to resolve.
     * @return  \Octris\Db\Type\DataObject|bool                             Data object or false if reference could not he resolved.
     */
    public function resolve(\Octris\Db\Type\DbRef $dbref);

    /**
     * Return instance of collection object.
     *
     * @param   string          $name                               Name of collection to return instance of.
     * @return  \Octris\Db\Type\Collection                          Instance of database collection.
     */
    public function getCollection($name);
}
