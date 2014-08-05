<?php

namespace Tomahawk\Bundle\MigrationsBundle\Migration;

use Tomahawk\HttpKernel\Bundle\BundleInterface;
use Tomahawk\Generator\Generator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class MigrationGenerator extends Generator
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function generate(BundleInterface $bundle = null, $name)
    {
        $dir = $bundle->getPath();

        $migrationFolder = $dir .'/Migration/';

        if (!file_exists($migrationFolder)) {
            $this->filesystem->mkdir($migrationFolder);
        }

        $migrationFile = $migrationFolder.$name.'.php';

        if (file_exists($migrationFile)) {
            throw new \RuntimeException(sprintf('Migration "%s" already exists', $name));
        }

        $parameters = array(
            'namespace'  => $bundle->getNamespace(),
            'bundle'     => $bundle->getName(),
            'name'       => $name,
        );

        $this->renderFile('Migration.php.twig', $migrationFile, $parameters);
    }
}