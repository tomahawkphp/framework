<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Command;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\DropSchemaCommand;

class DropSchemaCommandTest extends ProxyCommand
{
    public function testCommand()
    {
        $command = new DropSchemaCommand();
        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));
    }
}