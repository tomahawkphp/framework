<?php

namespace Tomahawk\Database;

use Illuminate\Database\Capsule\Manager as DB;

class DatabaseManager
{
    /**
     * @param null $connection
     * @return \Illuminate\Database\Connection
     */
    public function connection($connection = null)
    {
        return DB::connection($connection);
    }

    /**
     * @param $table
     * @param $connection
     * @return \Illuminate\Database\Query\Builder
     */
    public function table($table, $connection = null)
    {
        return DB::connection($connection)->table($table);
    }

    /**
     * @param null $connection
     * @return \Illuminate\Database\Schema\Builder
     */
    public function schema($connection = null)
    {
        return DB::schema($connection);
    }
}