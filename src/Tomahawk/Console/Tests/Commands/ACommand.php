<?php

namespace Tomahawk\Console\Tests\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\DependencyInjection\ContainerInterface;

class ACommand extends Command implements ContainerAwareInterface
{
    public $input;
    public $output;
    protected $container;

    protected function configure()
    {
        $this
            ->setName('foo:bar')
            ->setDescription('The foo:bar command')
            ->setAliases(array('afoobar'))
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('interact called');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $output->writeln('called');
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
