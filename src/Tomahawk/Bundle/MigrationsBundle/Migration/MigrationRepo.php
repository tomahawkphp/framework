<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\MigrationsBundle\Migration;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Schema\Blueprint;

class MigrationRepo
{
    /**
     * @var ConnectionResolverInterface
     */
    protected $connectionResolver;

    protected $table;

    /**
     * @param ConnectionResolverInterface $connectionResolver
     * @param $table
     */
    public function __construct(ConnectionResolverInterface $connectionResolver, $table)
    {
        $this->connectionResolver = $connectionResolver;
        $this->table = $table;
    }

    /**
     * @return ConnectionResolverInterface
     */
    public function getConnectionResolver()
    {
        return $this->connectionResolver;
    }

    /**
     * Determine if the migration repository exists.
     *
     * @return bool
     */
    public function repositoryExists()
    {
        $schema = $this->connectionResolver->connection()->getSchemaBuilder();

        return $schema->hasTable($this->table);
    }

    /**
     * Create the migration repository data store.
     *
     * @param \Illuminate\Database\Schema\Blueprint $blueprint
     * @return bool
     */
    public function createRepository(Blueprint $blueprint)
    {
        $grammar = $this->connectionResolver->connection()->getSchemaGrammar();

        try
        {
            $blueprint->string('bundle');
            $blueprint->string('migration');
            $blueprint->integer('batch');
            $blueprint->build($this->connectionResolver->connection(), $grammar);

        }
        catch(\Exception $e)
        {
            return false;
        }

        return true;
    }

    /**
     * Get the ran migrations.
     *
     * @return array
     */
    public function getRan()
    {
        return $this->table()->lists('migration');
    }

    /**
     * Log that a migration was run.
     *
     * @param  string  $file
     * @param  int     $batch
     * @return void
     */
    public function log($file, $batch)
    {
        $record = array('migration' => $file, 'batch' => $batch);

        $this->table()->insert($record);
    }

    /**
     * Remove a migration from the log.
     *
     * @param  object  $migration
     * @return void
     */
    public function delete($migration)
    {
        $this->table()->where('migration', $migration->migration)->delete();
    }

    /**
     * Get the next migration batch number.
     *
     * @return int
     */
    public function getNextBatchNumber()
    {
        return $this->getLastBatchNumber() + 1;
    }

    /**
     * Get the last migration batch number.
     *
     * @return int
     */
    public function getLastBatchNumber()
    {
        return $this->table()->max('batch');
    }

    /**
     * Get the last migration batch.
     *
     * @return array
     */
    public function getLast()
    {
        $query = $this->table()->where('batch', $this->getLastBatchNumber());

        return $query->orderBy('migration', 'desc')->get();
    }

    /**
     * Get all migrations
     *
     * @return array
     */
    public function getAll()
    {
        $query = $this->table();
        return $query->orderBy('migration', 'desc')->get();
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->table;
    }

    /**
     * Get a query builder for the migration table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function table()
    {
        return $this->connectionResolver->connection()->table($this->table);
    }

}