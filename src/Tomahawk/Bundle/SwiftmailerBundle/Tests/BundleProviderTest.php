<?php

namespace Tomahawk\Bundle\SwiftmailerBundle\Tests;

use Tomahawk\DependencyInjection\Container;
use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\SwiftmailerBundle\DependencyInjection\SwiftmailerBundleProvider;

class BundleProviderTest extends TestCase
{
    public function testBundleProvider()
    {
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');
        $config->expects($this->once())
            ->method('get')
            ->with('swiftmailer')
            ->will($this->returnValue(array(
                'transport' => 'null'
            )));

        $container = new Container();
        $container->set('config', $config);

        $provider = new SwiftmailerBundleProvider();
        $provider->register($container);

        $this->assertTrue($container->has('mailer.transport'));
        $this->assertTrue($container->has('mailer'));

        $this->assertInstanceOf('Swift_NullTransport', $container->get('mailer.transport'));
        $this->assertInstanceOf('Swift_Mailer', $container->get('mailer'));
    }
}
