<?php

namespace Tomahawk\Bundle\MonologBundle\Tests\DI;

use InvalidArgumentException;
use Monolog\Logger;
use Monolog\Handler\HandlerInterface;
use Psr\Log\LoggerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Bundle\MonologBundle\DependencyInjection\MonologServiceProvider;
use Tomahawk\Config\ConfigInterface;

class DependencyInjectionTest extends TestCase
{
    public function testLogger()
    {
        $config = $this->getConfig();

        $container = $this->getContainer();
        $container->set('config', $config);

        $provider = new MonologServiceProvider();
        $provider->register($container);

        $this->assertInstanceOf(LoggerInterface::class, $container->get('monolog'));
    }

    public function testDefaultHandler()
    {
        $config = $this->getConfig();

        $container = $this->getContainer();
        $container->set('config', $config);

        $provider = new MonologServiceProvider();
        $provider->register($container);

        $this->assertInstanceOf(HandlerInterface::class, $container->get('monolog.handler'));
    }

    public function testRotatingFileHandler()
    {
        $config = $this->getConfig('rotating_file');

        $container = $this->getContainer();
        $container->set('config', $config);

        $provider = new MonologServiceProvider();
        $provider->register($container);

        $this->assertInstanceOf(HandlerInterface::class, $container->get('monolog.handler'));
    }

    public function testFingersCrossedHandler()
    {
        $config = $this->getConfig('fingers_crossed');

        $container = $this->getContainer();
        $container->set('config', $config);

        $provider = new MonologServiceProvider();
        $provider->register($container);

        $this->assertInstanceOf(HandlerInterface::class, $container->get('monolog.handler'));
    }

    public function testCustomHandler()
    {
        $config = $this->getConfig('test');

        $container = $this->getContainer();
        $container->set('config', $config);

        $provider = new MonologServiceProvider();
        $provider->register($container);

        $this->assertInstanceOf(HandlerInterface::class, $container->get('monolog.handler'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown log handler "none_existent". Have you added it to the logging config?
     */
    public function testInvalidHandler()
    {
        $config = $this->getConfig('none_existent');

        $container = $this->getContainer();
        $container->set('config', $config);

        $provider = new MonologServiceProvider();
        $provider->register($container);

        $container->get('monolog.handler');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Log handler "invalid" not registered under "logger.my.non_existent_handler"
     */
    public function testInvalidHandlerService()
    {
        $config = $this->getConfig('invalid');

        $container = $this->getContainer();
        $container->set('config', $config);

        $provider = new MonologServiceProvider();
        $provider->register($container);

        $container->get('monolog.handler');
    }

    protected function getContainer()
    {
        $container = new Container();
        $container->set('kernel', $this->getKernel());
        $container->set('logger.my.handler', $this->getMock(HandlerInterface::class));

        return $container;
    }

    protected function getConfig($defaultHandler = 'stream')
    {
        $config = $this->getMock(ConfigInterface::class);

        $config->method('get')
            ->will($this->returnValueMap([
                ['logging.max_files', 10, 10],
                ['logging.level', Logger::ERROR, Logger::ERROR],
                ['logging.action_level', Logger::ERROR, Logger::ERROR],
                ['logging.action_handler', null, 'stream'],
                ['logging.path', null, __DIR__],
                ['logging.handler', null, $defaultHandler],
                ['logging.custom_handlers', null, [
                        'test'   => 'logger.my.handler',
                        'invalid' => 'logger.my.non_existent_handler'
                    ]
                ],
            ]));

        return $config;
    }

    protected function getKernel()
    {
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();

        $kernel->expects($this->any())
            ->method('getEnvironment')
            ->will($this->returnValue('prod'));

        $kernel->expects($this->any())
            ->method('getRootDir')
            ->will($this->returnValue(__DIR__));

        $kernel->expects($this->any())
            ->method('getRoutePaths')
            ->will($this->returnValue(array(
                __DIR__ .'/Resources/bundleroutes/routes.php'
            )));

        return $kernel;
    }
}
