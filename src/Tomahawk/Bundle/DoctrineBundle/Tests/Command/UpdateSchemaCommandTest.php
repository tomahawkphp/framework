<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Command;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\UpdateSchemaCommand;

class UpdateSchemaCommandTest extends ProxyCommand
{
    public function testCommand()
    {
        $command = new UpdateSchemaCommand();
        $commandTester = $this->getCommandTester($command);
        //$commandTester->execute(array('command' => $command->getName()));
    }
}
