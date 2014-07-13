<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\GeneratorBundle\Command;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
//use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Tomahawk\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Tomahawk\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Tomahawk\Bundle\GeneratorBundle\Generator\BundleGenerator;
use Tomahawk\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Component\Console\Command\Command;


/**
 * Base class for generator commands.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class GeneratorCommand extends ContainerAwareCommand
{
    /**
     * @var Generator
     */
    private $generator;

    // only useful for unit tests
    public function setGenerator(Generator $generator)
    {
        $this->generator = $generator;
    }

    protected abstract function createGenerator();

    /**
     * @param BundleInterface $bundle
     * @return BundleGenerator
     */
    protected function getGenerator(BundleInterface $bundle = null)
    {
        if (null === $this->generator) {
            $this->generator = $this->createGenerator();
            $this->generator->setSkeletonDirs($this->getSkeletonDirs($bundle));
        }

        return $this->generator;
    }

    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $skeletonDirs = array();

        if (isset($bundle) && is_dir($dir = $bundle->getPath().'/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        if (is_dir($dir = $this->getContainer()->get('kernel')->getRootdir().'/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        $skeletonDirs[] = __DIR__.'/../Resources/skeleton';
        $skeletonDirs[] = __DIR__.'/../Resources';

        return $skeletonDirs;
    }

    protected function getQuestionHelper()
    {
        $dialog = null;

        if ($this->getHelperSet()->has('question')) {
            $dialog = $this->getHelperSet()->get('question');
        }

        if (!$dialog || get_class($dialog) !== 'Tomahawk\Bundles\GeneratorBundle\Command\Helper\QuestionHelper') {
            $this->getHelperSet()->set($dialog = new QuestionHelper());
        }

        return $dialog;
    }
}