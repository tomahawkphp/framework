<?php

namespace Tomahawk\Bundle\MigrationsBundle\Migration;

use Tomahawk\HttpKernel\Bundle\BundleInterface;

class MigrationReference
{
    protected $bundle;
    protected $fullPath;

    public function __construct(BundleInterface $bundle, $fullPath)
    {
        $this->bundle = $bundle;
        $this->fullPath = $fullPath;
    }

    /**
     * Get full path to migration
     *
     * @return mixed
     */
    public function getPath()
    {
        return $this->fullPath;
    }

    public function getName()
    {
        return str_replace('.php', '', basename($this->getPath()));
    }

    /**
     * Get Class name of migration
     *
     * @return mixed
     */
    public function getClass()
    {
        return $this->bundle->getNamespace() . '\\Migration\\' . str_replace('.php', '', basename($this->getPath()));
    }
}