<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\ConvertMappingCommand;
use Tomahawk\Bundle\DoctrineBundle\Tests\ProxyCommand;

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