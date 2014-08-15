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

use Tomahawk\HttpKernel\Kernel;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Tomahawk\Bundle\MigrationsBundle\Migration\MigrationReference;

class Migrator
{
    /**
     * @var MigrationRepo
     */
    protected $repository;

    /**
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var array
     */
    protected $notes = array();

    /**
     * @param MigrationRepo $repository
     * @param Finder $finder
     * @param Kernel $kernel
     */
    public function __construct(MigrationRepo $repository, Finder $finder, Kernel $kernel)
    {
        $this->repository = $repository;
        $this->finder = $finder;
        $this->kernel = $kernel;
    }

    /**
     * @return array
     */
    public function getNotes()
    {
        return $this->notes;
    }

    public function run()
    {
        $this->notes = array();

        $ran = $this->repository->getRan();

        $files = $this->getMigrationFiles();

        $migrations = array_diff($files, $ran);

        $this->runMigrationList($migrations);
    }

    public function rollback()
    {
        $this->notes = array();

        $migrations = $this->repository->getLast();

        if (count($migrations) == 0)
        {
            $this->note('<info>Nothing to rollback.</info>');

            return count($migrations);
        }

        foreach ($migrations as $migration)
        {
            $bundle = $this->kernel->getBundle($migration->bundle);

            $migrationPath = $bundle->getPath() .'/Migration/' . $migration->migration . '.php';

            $migrationReference = new MigrationReference($bundle, $migrationPath);

            $this->runDown($migrationReference, $migration);
        }

        return count($migrations);
    }

    public function reset()
    {
        $this->notes = array();

        $migrations = $this->repository->getAll();

        if (count($migrations) == 0)
        {
            $this->note('<info>Nothing to rollback.</info>');

            return count($migrations);
        }

        foreach ($migrations as $migration)
        {
            $bundle = $this->kernel->getBundle($migration->bundle);

            $migrationPath = $bundle->getPath() .'/Migration/' . $migration->migration . '.php';

            $migrationReference = new MigrationReference($bundle, $migrationPath);

            $this->runDown($migrationReference, $migration);
        }

        return count($migrations);
    }

    protected function runUp(MigrationReference $migrationReference, $batch)
    {
        /**
         * @var MigrationInterface $migration
         */
        require_once($migrationReference->getPath());
        $class = $migrationReference->getClass();
        $migration = new $class();
        $migration->up($this->getSchemaBuilder());

        // Once we have run a migrations class, we will log that it was run in this
        // repository so that we don't try to run it next time we do a migration
        // in the application. A migration repository keeps the migrate order.
        $this->repository->log($migrationReference, $batch);

        $this->note(sprintf('<info>Migrated:</info> %s', $migrationReference->getName()));

    }

    /**
     * Run "down" a migration instance.
     *
     * @param  MigrationReference $migrationReference
     * @param $migration
     * @return void
     */
    protected function runDown(MigrationReference $migrationReference, $migration)
    {
        /**
         * @var MigrationInterface $migrationClass
         */
        require_once($migrationReference->getPath());
        $class = $migrationReference->getClass();
        $migrationClass = new $class();
        $migrationClass->down($this->getSchemaBuilder());

        $file = $migration->migration;

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

    public function getMigrationFiles()
    {
        $files = array();

        foreach ($this->kernel->getBundles() as $bundle)
        {
            $directory = $bundle->getPath() . '/Migration';
            $finder = $this->finder->files()->in($directory)->name('*.php')->sortByName();

            foreach ($finder as $file) {

                /**
                 * @var SplFileInfo $file
                 */
                $files[] = new MigrationReference($bundle, $file->getRealPath());
            }
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
     * @return \Illuminate\Database\Schema\Builder
     */
    protected function getSchemaBuilder()
    {
        return $this->repository->getConnectionResolver()->connection()->getSchemaBuilder();
    }
}