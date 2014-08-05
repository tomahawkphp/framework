<?php

/*
 * This file is part of the Tomahawk package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\GeneratorBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Tomahawk\Bundle\GeneratorBundle\Generator\BundleGenerator;
use Tomahawk\Bundle\GeneratorBundle\Command\Validators;

/**
 * Generates bundles.
 *
 * @author Tom Ellis
 *
 * Based on the Sensio Labs - SensioGeneratorBundle
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class GenerateBundleCommand extends GenerateCommand
{

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputOption('namespace', '', InputOption::VALUE_REQUIRED, 'The namespace of the bundle to create'),
                new InputOption('dir', '', InputOption::VALUE_REQUIRED, 'The directory where to create the bundle'),
                new InputOption('bundle-name', '', InputOption::VALUE_REQUIRED, 'The optional bundle name'),
            ))
            ->setDescription('Generates a bundle')
            ->setName('generate:bundle');
    }

    /**
     * @see Command
     *
     * @throws \InvalidArgumentException When namespace doesn't end with Bundle
     * @throws \RuntimeException         When bundle can't be executed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach (array('namespace', 'dir', 'bundle-name') as $option) {
            if (null === $input->getOption($option)) {
                throw new \RuntimeException(sprintf('The "%s" option must be provided.', $option));
            }
        }

        $namespace = Validators::validateBundleNamespace($input->getOption('namespace'));

        if (!$bundle = $input->getOption('bundle-name')) {
            $bundle = strtr($namespace, array('\\' => ''));
        }

        $bundle = Validators::validateBundleName($bundle);
        $dir = Validators::validateTargetDir($input->getOption('dir'), $bundle, $namespace);

        $output->writeln('Bundle generation');

        if (!$this->container->get('filesystem')->isAbsolutePath($dir)) {
            $dir = getcwd().'/'.$dir;
        }

        $generator = $this->getGenerator();
        $generator->generate($namespace, $bundle, $dir);

        $output->writeln('Generating the bundle code: <info>OK</info>');

        $this->checkAutoloader($output, $namespace, $bundle, $dir);
    }

    protected function checkAutoloader(OutputInterface $output, $namespace, $bundle, $dir)
    {
        $output->write('Checking that the bundle is autoloaded: ');
        if (!class_exists($namespace.'\\'.$bundle)) {
            return array(
                '- Edit the <comment>composer.json</comment> file and register the bundle',
                '  namespace in the "autoload" section:',
                '',
            );
        }
    }

    /**
     * @return BundleGenerator
     */
    public function getGenerator()
    {
        return $this->container->get('bundle_generator');
    }
}