<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\InfoCommand;
use Tomahawk\Bundle\DoctrineBundle\Tests\ProxyCommand;

class InfoCommandTest extends ProxyCommand
{
    public function testCommand()
    {
        $command = new InfoCommand();
        $commandTester = $this->getCommandTester($command);

        //$commandTester->execute(array('command' => $command->getName()));
    }
}