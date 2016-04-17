<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Command;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\EnsureProductionSettingsCommand;

class EnsureProductionSettingsCommandTest extends ProxyCommand
{
    public function testCommand()
    {
        $command = new EnsureProductionSettingsCommand();
        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));
    }
}