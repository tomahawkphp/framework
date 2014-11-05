<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaCommand;
use Tomahawk\Bundle\DoctrineBundle\Tests\ProxyCommand;

class CreateSchemaCommandTest extends ProxyCommand
{
    public function testCommand()
    {
        $command = new CreateSchemaCommand();
        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));
    }
}