<?php

namespace Tomahawk\Bundle\GeneratorBundle\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Tomahawk\Generator\Generator;
use Tomahawk\HttpKernel\Bundle\BundleInterface;

/**
 * Generates a model.
 */
class ModelGenerator extends Generator
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Constructor.
     *
     * @param Filesystem $filesystem A Filesystem instance
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function generate(BundleInterface $bundle, $model)
    {
        $dir = $bundle->getPath();

        $modelFile = $dir.'/Model/'.$model.'.php';
        if (file_exists($modelFile)) {
            throw new \RuntimeException(sprintf('Model "%s" already exists', $model));
        }

        $parameters = array(
            'namespace'  => $bundle->getNamespace(),
            'bundle'     => $bundle->getName(),
            'model'      => $model,
        );

        $this->renderFile('model/Model.php.twig', $modelFile, $parameters);
    }
}
