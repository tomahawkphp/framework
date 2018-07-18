<?php

namespace Tomahawk\Console\Test\Commands;

use Tomahawk\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('foo:bar1')
            ->setDescription('The foo:bar1 command')
            ->setAliases(array('afoobar1'))
        ;
    }

    public function handle()
    {
        $this->output->writeln('execute called');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('interact called');
    }

}