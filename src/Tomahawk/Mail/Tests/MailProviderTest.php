<?php

namespace Tomahawk\Mail\Tests;

use Tomahawk\Config\ConfigInterface;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Mail\MailServiceProvider;
use PHPUnit\Framework\TestCase;

class MailProviderTest extends TestCase
{
    public function testBundleProvider()
    {
        $config = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $config->expects($this->once())
            ->method('get')
            ->with('swiftmailer')
            ->will($this->returnValue(array(
                'transport' => 'null'
            )));

        $container = new Container();
        $container->set('config', $config);

        $provider = new MailServiceProvider();
        $provider->register($container);

        $this->assertTrue($container->has('mailer.transport'));
        $this->assertTrue($container->has('mailer'));

        $this->assertInstanceOf('Swift_NullTransport', $container->get('mailer.transport'));
        $this->assertInstanceOf('Swift_Mailer', $container->get('mailer'));
    }
}
