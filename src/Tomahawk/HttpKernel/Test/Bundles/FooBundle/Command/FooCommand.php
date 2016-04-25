<?php

namespace Tomahawk\HttpKernel\Test\Bundles\FooBundle\Command;

use Tomahawk\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Tomahawk\DependencyInjection\ContainerAwareInterface;

class FooCommand extends Command implements ContainerAwareInterface
{
    protected $container;

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
