<?php

namespace Tomahawk\Log\Tests\Factory;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\Log\Factory\HandlerFactory;

/**
 * Class HandlerFactoryTest
 *
 * @package Tomahawk\Log\Tests\Factory
 */
class HandlerFactoryTest extends TestCase
{
    public function testCreateSingleDriver()
    {
        $container = $this->createMock(ContainerInterface::class);
        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['logging.channels.single', null, [
                    'driver' => 'single',
                    'path' => 'logs/laravel.log',
                    'level' => 'debug',
                ]],
            ])
        ;

        $factory = new HandlerFactory(
            $container,
            $configManger,
            []
        );

        $logger = $factory->make('single');

        $this->assertInstanceOf(Logger::class, $logger);
        $this->assertCount(1, $logger->getHandlers());
    }

    public function testCreateDailyDriver()
    {
        $container = $this->createMock(ContainerInterface::class);
        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['logging.channels.daily', null, [
                    'driver' => 'daily',
                    'path' => 'logs/laravel.log',
                    'level' => 'debug',
                    'days' => 7,
                ]],
            ])
        ;

        $factory = new HandlerFactory(
            $container,
            $configManger,
            []
        );

        $logger = $factory->make('daily');

        $this->assertInstanceOf(Logger::class, $logger);
        $this->assertCount(1, $logger->getHandlers());
    }

    public function testCreateStreamDriver()
    {
        $container = $this->createMock(ContainerInterface::class);
        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['logging.channels.stream', null, [
                    'driver' => 'stream',
                    'stream' => 'php://stderr',
                ]],
            ])
        ;

        $factory = new HandlerFactory(
            $container,
            $configManger,
            []
        );

        $logger = $factory->make('stream');

        $this->assertInstanceOf(Logger::class, $logger);
        $this->assertCount(1, $logger->getHandlers());
    }

    public function testCreateSyslogDriver()
    {
        $container = $this->createMock(ContainerInterface::class);
        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['logging.channels.syslog', null, [
                    'driver' => 'syslog',
                    'level' => 'debug',
                ]],
            ])
        ;

        $factory = new HandlerFactory(
            $container,
            $configManger,
            []
        );

        $logger = $factory->make('syslog');

        $this->assertInstanceOf(Logger::class, $logger);
        $this->assertCount(1, $logger->getHandlers());
    }

    public function testCreateErrorlogDriver()
    {
        $container = $this->createMock(ContainerInterface::class);
        $configManger = $this->createMock(ConfigInterface::class);

        $configManger->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['logging.channels.errorlog', null, [
                    'driver' => 'errorlog',
                    'level' => 'debug',
                ]],
            ])
        ;

        $factory = new HandlerFactory(
            $container,
            $configManger,
            []
        );

        $logger = $factory->make('errorlog');

        $this->assertInstanceOf(Logger::class, $logger);
        $this->assertCount(1, $logger->getHandlers());
    }
}
