<?php

namespace Tomahawk\Generator;

use Symfony\Component\Filesystem\Filesystem;

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

    /**
     * @param string $directory
     * @param string $namespace
     * @param string $model
     */
    public function generate(string $directory, string $namespace, string $model)
    {
        $modelFile = $directory.'/'.$model.'.php';

        if (file_exists($modelFile)) {
            throw new \RuntimeException(sprintf('Model "%s" already exists', $model));
        }

        $parameters = [
            'namespace' => $namespace,
            'model' => $model,
        ];

        $this->renderFile('model/Model.php.twig', $modelFile, $parameters);
    }
}
