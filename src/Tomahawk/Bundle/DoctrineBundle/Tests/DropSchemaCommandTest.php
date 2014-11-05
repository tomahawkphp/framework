<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\DropSchemaCommand;
use Tomahawk\Bundle\DoctrineBundle\Tests\ProxyCommand;

class DropSchemaCommandTest extends ProxyCommand
{
    public function testCommand()
    {
        $command = new DropSchemaCommand();
        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));
    }
}