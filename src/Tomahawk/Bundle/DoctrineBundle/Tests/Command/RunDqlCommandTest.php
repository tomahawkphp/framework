<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Command;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\RunDqlCommand;

class RunDqlCommandTest extends ProxyCommand
{
    /**
     * @expectedException \RuntimeException
     */
    public function testCommand()
    {
        $command = new RunDqlCommand();
        $commandTester = $this->getCommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));
    }
}
