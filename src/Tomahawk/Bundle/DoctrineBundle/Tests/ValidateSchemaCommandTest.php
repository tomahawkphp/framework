<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\ValidateSchemaCommand;
use Tomahawk\Bundle\DoctrineBundle\Tests\ProxyCommand;

class ValidateSchemaCommandTest extends ProxyCommand
{
    public function testCommand()
    {
        $command = new ValidateSchemaCommand();
        $commandTester = $this->getCommandTester($command);
        //$commandTester->execute(array('command' => $command->getName()));
    }
}
