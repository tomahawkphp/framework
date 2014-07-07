<?php

namespace Tomahawk\Console;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class Output extends ConsoleOutput implements OutputInterface
{
    public function info($string)
    {
        $this->writeln('<fg=green>'.$string.'</fg=green>');
    }

    public function success($string)
    {
        $this->writeln('<info>'.$string.'</info>');
    }

    public function question($string)
    {
        $this->writeln('<question>'.$string.'</question>');
    }

    public function error($string)
    {
        $this->writeln('<error>'.$string.'</error>');
    }

}