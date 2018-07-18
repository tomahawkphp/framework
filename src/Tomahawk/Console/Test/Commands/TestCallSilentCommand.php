<?php

namespace Tomahawk\Console\Test\Commands;

use Tomahawk\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCallSilentCommand extends Command
{
    public $input;
    public $output;

    protected function configure()
    {
        $this
            ->setName('foo:test-call-silent')
            ->setDescription('The foo:test-call-silent command')
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('interact called');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('execute called');
        return $this->callSilent('foo:bar1');
    }
}
