<?php

namespace Tomahawk\Bundles\MigrationsBundle\Migration;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Migrator
{
    protected $repository;

    protected $finder;

    protected $notes;

    public function __construct(MigrationRepo $repository, Finder $finder)
    {
        $this->repository = $repository;
        $this->finder = $finder;
    }

    /**
     * @param MigrationRepo $repository
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return MigrationRepo
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    public function rollback()
    {
        $this->notes = array();

        // We want to pull in the last batch of migrations that ran on the previous
        // migration operation. We'll then reverse those migrations and run each
        // of them "down" to reverse the last migration "operation" which ran.
        $migrations = $this->repository->getLast();

        if (count($migrations) == 0)
        {
            $this->note('<info>Nothing to rollback.</info>');

            return count($migrations);
        }

        // We need to reverse these migrations so that they are "downed" in reverse
        // to what they run on "up". It lets us backtrack through the migrations
        // and properly reverse the entire database schema operation that ran.
        foreach ($migrations as $migration)
        {
            $this->runDown((object) $migration);
        }

        return count($migrations);
    }

    public function reset()
    {
        $this->notes = array();

        // We want to pull in the last batch of migrations that ran on the previous
        // migration operation. We'll then reverse those migrations and run each
        // of them "down" to reverse the last migration "operation" which ran.
        $migrations = $this->repository->getAll();

        if (count($migrations) == 0)
        {
            $this->note('<info>Nothing to rollback.</info>');

            return count($migrations);
        }

        // We need to reverse these migrations so that they are "downed" in reverse
        // to what they run on "up". It lets us backtrack through the migrations
        // and properly reverse the entire database schema operation that ran.
        foreach ($migrations as $migration)
        {
            $this->runDown((object) $migration);
        }

        return count($migrations);
    }

    public function run($path)
    {
        $this->notes = array();

        $ran = $this->repository->getRan();

        $files = $this->getMigrationFiles($path);

        $migrations = array_diff($files, $ran);

        $this->runMigrationList($migrations);
    }

    protected function runUp($file, $batch)
    {
        // First we will resolve a "real" instance of the migration class from this
        // migration file name. Once we have the instances we can run the actual
        // command such as "up" or "down", or we can just simulate the action.
        //$migration = $this->resolve($file);

        /**
         * @var MigrationInterface $migration
         */
        $migration = new $file();
        $migration->up($this->getRepository()->getConnectionResolver()->connection()->getSchemaBuilder());

        // Once we have run a migrations class, we will log that it was run in this
        // repository so that we don't try to run it next time we do a migration
        // in the application. A migration repository keeps the migrate order.
        $this->repository->log($file, $batch);

        $this->note("<info>Migrated:</info> $file");

    }

    /**
     * Run "down" a migration instance.
     *
     * @param  object  $migration
     * @return void
     */
    protected function runDown($migration)
    {
        $file = $migration->migration;

        /**
         * @var MigrationInterface $migration
         */
        $migration = new $file();

        $migration->down($this->getRepository()->getConnectionResolver()->connection()->getSchemaBuilder());

        // Once we have successfully run the migration "down" we will remove it from
        // the migration repository so it will be considered to have not been run
        // by the application then will be able to fire by any later operation.
        $this->repository->delete($migration);

        $this->note("<info>Rolled back:</info> $file");
    }

    public function runMigrationList($migrations)
    {
        if (count($migrations) == 0)
        {
            $this->note('<info>Nothing to migrate.</info>');

            return;
        }


        $batch = $this->repository->getNextBatchNumber();

        // Once we have the array of migrations, we will spin through them and run the
        // migrations "up" so the changes are made to the databases. We'll then log
        // that the migration was run so we don't repeat it next time we execute.
        foreach ($migrations as $file)
        {
            $this->runUp($file, $batch);
        }
    }

    public function getMigrationFiles($path)
    {

        $finder = $this->finder->files()->in($path)->name('*.php')->sortByName();

        if (!$finder) {
            return array();
        }

        $files = array();

        foreach ($finder as $file) {

            /**
             * @var SplFileInfo $file
             */
            $files[] = str_replace('.php', '', basename($file->getFilename()));
        }

        return $files;
    }

    /**
     * Raise a note event for the migrator.
     *
     * @param  string  $message
     * @return void
     */
    protected function note($message)
    {
        $this->notes[] = $message;
    }

    /**
     * @param $file
     * @return mixed
     */
    public function resolve($file)
    {
        $file = implode('_', array_slice(explode('_', $file), 4));

        $class = $this->studly($file);

        return new $class;
    }

    /**
     * Convert a value to studly caps case.
     *
     * @param  string  $value
     * @return string
     */
    protected function studly($value)
    {
        $value = ucwords(str_replace(array('-', '_'), ' ', $value));

        return str_replace(' ', '', $value);
    }
}