<?php

namespace Tomahawk\Bundle\MonologBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DI\Container;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\Bundle\MonologBundle\MonologBundle;
use Tomahawk\Config\ConfigInterface;

class MonologProviderTest extends TestCase
{
    protected function getConfig($defaultHandler = 'stream')
    {
        $config = $this->getMock(ConfigInterface::class);

        /*$config->method('get')
            ->will($this->returnValueMap([
                ['security.provider', null, $defaultProvider],
                ['security.providers', null,
                    [
                        'memory' => ['users' => $this->users],
                        'my_provider' => ['service' => 'auth.my_provider'],
                        'my_non_existent_provider' => ['service' => 'auth.my_non_existent_provider'],
                    ]
                ],
                ['security.providers.memory', null, [
                        'users' => $this->users
                    ]
                ],
            ]));*/

        return $config;
    }
}
