<?php

namespace Tomahawk\HttpKernel\Test\Bundles\FooBundle\Command;

use Tomahawk\DI\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tomahawk\DI\ContainerAwareInterface;

class FooCommand extends Command implements ContainerAwareInterface
{
    protected function configure()
    {
        $this
            ->setName('foo:bar')
            ->setDescription('Foo Bar.');
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}