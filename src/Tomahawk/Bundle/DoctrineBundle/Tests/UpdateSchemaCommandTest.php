<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\UpdateSchemaCommand;
use Tomahawk\Bundle\DoctrineBundle\Tests\ProxyCommand;

class UpdateSchemaCommandTest extends ProxyCommand
{
    public function testCommand()
    {
        $command = new UpdateSchemaCommand();
        $commandTester = $this->getCommandTester($command);
        //$commandTester->execute(array('command' => $command->getName()));
    }
}
