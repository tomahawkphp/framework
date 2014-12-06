<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Database;

use Illuminate\Database\ConnectionResolverInterface;

class DatabaseManager
{
    /**
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected $database;

    public function __construct(ConnectionResolverInterface $database)
    {
        $this->database = $database;
    }

    /**
     * @param null $connection
     * @return \Illuminate\Database\Connection
     */
    public function connection($connection = null)
    {
        return $this->database->connection($connection);
    }

    /**
     * @param $table
     * @param $connection
     * @return \Illuminate\Database\Query\Builder
     */
    public function table($table, $connection = null)
    {
        return $this->connection($connection)->table($table);
    }

    /**
     * @param null $connection
     * @return \Illuminate\Database\Schema\Builder
     */
    public function schema($connection = null)
    {
        return $this->database->connection($connection)->getSchemaBuilder();
    }
}
