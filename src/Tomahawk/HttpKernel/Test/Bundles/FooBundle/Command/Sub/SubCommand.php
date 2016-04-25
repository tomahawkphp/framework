<?php

namespace Tomahawk\HttpKernel\Test\Bundles\FooBundle\Command\Sub;

use Tomahawk\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Tomahawk\DependencyInjection\ContainerAwareInterface;

class SubCommand extends Command implements ContainerAwareInterface
{
    protected $container;

    protected function configure()
    {
        $this
            ->setName('subfoo:bar')
            ->setDescription('Foo Bar.');
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
