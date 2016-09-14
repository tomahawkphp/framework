<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Command;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaCommand;

class CreateSchemaCommandTest extends ProxyCommand
{
    public function testCommand()
    {
        $command = new CreateSchemaCommand();
        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));
    }
}