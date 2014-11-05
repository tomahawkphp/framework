<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\EnsureProductionSettingsCommand;
use Tomahawk\Bundle\DoctrineBundle\Tests\ProxyCommand;

class EnsureProductionSettingsCommandTest extends ProxyCommand
{
    public function testCommand()
    {
        $command = new EnsureProductionSettingsCommand();
        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));
    }
}