<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\ClearQueryCacheCommand;
use Tomahawk\Bundle\DoctrineBundle\Tests\ProxyCommand;

class ClearQueryCacheCommandTest extends ProxyCommand
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCommand()
    {
        $command = new ClearQueryCacheCommand();
        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));
    }
}