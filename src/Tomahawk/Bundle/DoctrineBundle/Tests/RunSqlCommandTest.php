<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\RunSqlCommand;
use Tomahawk\Bundle\DoctrineBundle\Tests\ProxyCommand;

class RunSqlCommandTest extends ProxyCommand
{
    /**
     * @expectedException \RuntimeException
     */
    public function testCommand()
    {
        $command = new RunSqlCommand();
        $commandTester = $this->getCommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));
    }
}
