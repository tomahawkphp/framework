<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\FrameworkBundle\Command;

use Tomahawk\DI\ContainerInterface;
use Tomahawk\DI\ContainerAwareInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheClearCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected function configure()
    {
        $this
            ->setName('cache:clear')
            ->setDescription('Clear Cache.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container->get('cache')->flush();

        $output->writeln('<info>Cache has been flushed.</info>');
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
