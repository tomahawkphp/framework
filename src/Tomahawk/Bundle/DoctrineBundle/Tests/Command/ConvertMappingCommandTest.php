<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Command;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\ConvertMappingCommand;

class ConvertMappingCommandTest extends ProxyCommand
{
    /**
     * @expectedException \RuntimeException
     */
    public function testCommand()
    {
        $command = new ConvertMappingCommand();
        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));
    }
}