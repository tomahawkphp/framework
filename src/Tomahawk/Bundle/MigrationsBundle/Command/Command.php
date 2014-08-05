<?php

namespace Tomahawk\Bundle\MigrationsBundle\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;

class Command extends BaseCommand implements ContainerAwareInterface
{

    /**
     * @var ContainerInterface|null
     */
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}