<?php

namespace Tomahawk\Bundles\MigrationsBundle\Migration;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Schema\Blueprint;

class MigrationRepo
{
    /**
     * @var ConnectionResolverInterface
     */
    protected $connectionResolver;

    protected $table;

    public function __construct(ConnectionResolverInterface $connectionResolver, $table)
    {
        $this->connectionResolver = $connectionResolver;
        $this->table = $table;
    }

    /**
     * @param ConnectionResolverInterface $connectionResolver
     */
    public function setConnectionResolver($connectionResolver)
    {
        $this->connectionResolver = $connectionResolver;
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
        $schema = $this->getConnectionResolver()->connection()->getSchemaBuilder();

        return $schema->hasTable($this->table);
    }

    /**
     * Create the migration repository data store.
     *
     * @return void
     */
    public function createRepository()
    {
        $schema = $this->getConnectionResolver()->connection()->getSchemaBuilder();

        $schema->create($this->table, function(Blueprint $table)
        {
            // The migrations table is responsible for keeping track of which of the
            // migrations have actually run for the application. We'll create the
            // table to hold the migration file's path as well as the batch ID.
            $table->string('migration');

            $table->integer('batch');
        });
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
        $query = $this->table()->where('migration', $migration->migration)->delete();
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

    public function getAll()
    {
        $query = $this->table();
        return $query->orderBy('migration', 'desc')->get();
    }

    /**
     * Get a query builder for the migration table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function table()
    {
        return $this->getConnectionResolver()->connection()->table($this->table);
    }
}