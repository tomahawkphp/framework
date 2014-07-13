<?php

namespace Tomahawk\Bundle\FrameworkBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;


class RoutingCommand extends Command implements ContainerAwareInterface
{
    protected $container;

    protected function configure()
    {
        $this->setName('routing:view')
            ->setDescription('View routes.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $table = new Table($output);
        $table
            ->setHeaders(array('ISBN', 'Title', 'Author'))
            ->setRows(array(
                array('99921-58-10-7', 'Divine Comedy', 'Dante Alighieri'),
                array('9971-5-0210-0', 'A Tale of Two Cities', 'Charles Dickens'),
                array('960-425-059-0', 'The Lord of the Rings', 'J. R. R. Tolkien'),
                array('80-902734-1-6', 'And Then There Were None', 'Agatha Christie'),
            ))
        ;
        $table->render();

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