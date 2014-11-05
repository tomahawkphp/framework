<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\RunDqlCommand;
use Tomahawk\Bundle\DoctrineBundle\Tests\ProxyCommand;

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
