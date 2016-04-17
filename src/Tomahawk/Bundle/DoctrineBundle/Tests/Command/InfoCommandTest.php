<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Command;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\InfoCommand;

class InfoCommandTest extends ProxyCommand
{
    public function testCommand()
    {
        $command = new InfoCommand();
        $commandTester = $this->getCommandTester($command);

        //$commandTester->execute(array('command' => $command->getName()));
    }
}