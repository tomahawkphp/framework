<?php

namespace Tomahawk\Log\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Log\LogManagerInterface;
use Tomahawk\Log\LogServiceProvider;

/**
 * Class LogServiceProviderTest
 *
 * @package Tomahawk\Log\Tests
 */
class LogServiceProviderTest extends TestCase
{
    public function testProvider()
    {
        $container = new Container();

        $provider = new LogServiceProvider();

        $provider->register($container);

        $this->assertTrue($container->hasAlias('logger'));
        $this->assertTrue($container->hasAlias(LoggerInterface::class));
        $this->assertTrue($container->has(LogManagerInterface::class));

    }
}
