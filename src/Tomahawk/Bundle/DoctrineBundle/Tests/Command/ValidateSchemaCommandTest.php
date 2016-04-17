<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Command;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\ValidateSchemaCommand;

class ValidateSchemaCommandTest extends ProxyCommand
{
    public function testCommand()
    {
        $command = new ValidateSchemaCommand();
        $commandTester = $this->getCommandTester($command);
        //$commandTester->execute(array('command' => $command->getName()));
    }
}
