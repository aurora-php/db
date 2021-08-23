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

namespace Octris\Db;

use \Octris\Db\Device\Type;

/**
 * Database devices base class.
 *
 * @copyright   copyright (c) 2012-present by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
abstract class Device
{
    /**
     * Registry for host configurations.
     *
     * @type    array
     */
    protected $hosts = [];

    /**
     * Active connections.
     *
     * @type    array
     */
    protected $connections = [];

    /**
     * Registry of free database connections.
     *
     * @type    array
     */
    protected $pool = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->hosts[Type::MASTER->value] = [];
        $this->hosts[Type::SLAVE->value] = [];

        $this->pool[Type::MASTER->value] = [];
        $this->pool[Type::SLAVE->value] = [];
    }

    /**
     * Validate connection parameters.
     *
     * @param   \Octris\PropertyCollection  $options
     * @return  bool
     */
    abstract protected function validateOptions(\Octris\PropertyCollection $options): bool;

    /**
     * Add host configuration of specified type.
     *
     * @param   string                      $type               Type of host to add.
     * @param   \Octris\PropertyCollection  $options            Host configuration options for database host.
     * @param   bool                        $master_as_slave    Whether to add a master connection as slave, too.
     * @throws  \InvalidArgumentException
     */
    public function addHost(Type $type, \Octris\PropertyCollection $options, bool $master_as_slave = true)
    {
        if (!$this->validateOptions($options)) {
            throw new \InvalidArgumentException('Invalid options specified');
        }

        $this->hosts[$type->value][] = $options;

        if ($master_as_slave && $type->isMaster()) {
            $this->hosts[Type::SLAVE->value][] = $options;
        }
    }

    /**
     * Create a new database connection for specified configuration options.
     *
     * @param   \Octris\PropertyCollection          $options        Host configuration options.
     * @return  \Octris\Db\Device\ConnectionInterface               Connection to a database.
     */
    abstract protected function createConnection(\Octris\PropertyCollection $options) : \Octris\Db\Device\ConnectionInterface;

    /**
     * Return a database connection of specified type.
     *
     * @param   string                              $type           Optional type of connection.
     * @return  \Octris\Db\Device\ConnectionInterface               Connection to a database.
     */
    public function getConnection(Type $type = Type::MASTER) : \Octris\Db\Device\ConnectionInterface
    {
        if (!($cn = array_shift($this->pool[$type->value]))) {
            // no more connections in the pool, create new one
            if (count($this->hosts[$type->value]) == 0) {
                throw new \Exception(sprintf('No database configuration available for connection to a "%s" host.', $type));
            }

            shuffle($this->hosts[$type->value]);

            $cn = $this->createConnection($this->hosts[$type->value][0]);

            if (!($cn instanceof \Octris\Db\Device\ConnectionInterface)) {
                throw new \Exception('connection handler needs to implement interface "\Octris\Db\Device\ConnectionInterface"');
            }
        }

        $this->connections[spl_object_hash($cn)] = $type;

        return $cn;
    }

    /**
     * Release a connection, push it back into the pool.
     *
     * @param   \Octris\Db\Device\ConnectionInterface   $cn     Connection to release to pool.
     */
    public function release(\Octris\Db\Device\ConnectionInterface $cn)
    {
        $hash = spl_object_hash($cn);

        if (!isset($this->connections[$hash])) {
            throw new \Exception('Connection is not handled by this device');
        }

        if (!$cn->doPooling()) {
            array_push($this->pool[$this->connections[$hash]->value], $cn);
            unset($this->connections[$hash]);
        } else {
            $cn->close();
        }
    }
}
