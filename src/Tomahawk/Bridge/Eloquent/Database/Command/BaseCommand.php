<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bridge\Eloquent\Command;

use Illuminate\Database\DatabaseManager;
use Tomahawk\Console\ContainerAwareCommand;
use Tomahawk\DependencyInjection\ContainerInterface;

/**
 * Class BaseCommand
 * @package Tomahawk\Bridge\Eloquent\Command
 */
abstract class BaseCommand extends ContainerAwareCommand
{
    /**
     * Get all of the migration paths.
     *
     * @return array
     */
    protected function getMigrationPaths()
    {
        // Here, we will check to see if a path option has been defined. If it has we will
        // use the path relative to the root of the installation folder so our database
        // migrations may be run for any customized path from within the application.
        if ($this->input->hasOption('path') && $this->option('path')) {
            return collect($this->option('path'))->map(function ($path) {
                return ! $this->usingRealPath()
                    ? $this->laravel->basePath().'/'.$path
                    : $path;
            })->all();
        }
        return array_merge(
            $this->migrator->paths(), [$this->getMigrationPath()]
        );
    }


    /**
     * Determine if the given path(s) are pre-resolved "real" paths.
     *
     * @return bool
     */
    protected function usingRealPath()
    {
        return $this->input->hasOption('realpath') && $this->input->getOption('realpath');
    }

    /**
     * @return DatabaseManager
     */
    protected function getDatabaseManager()
    {
        return $this->container->get(DatabaseManager::class);
    }
}
