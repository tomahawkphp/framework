<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Command;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\RunSqlCommand;

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
