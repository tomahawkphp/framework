<?php

namespace Tomahawk\Framework\Tests;

use PHPUnit\Framework\TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Framework\TranslatorServiceProvider as TranslatorProvider;

class TranslatorServiceProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Framework\TranslatorServiceProvider
     */
    public function testProvider()
    {
        $tranlationsDir = [
            __DIR__ . '/Resources/translations',
        ];

        $config = $this->getConfig();
        $config
            ->method('get')
            ->will($this->returnValueMap([
                ['translation.locale', null, 'en'],
                ['translation.fallback_locale', null, 'en'],
                ['translation.translation_dirs', null, $tranlationsDir],
                ['translation.cache', null, false],
            ]));

        $container = new Container();
        $container->set('config', $config);

        $translatorProvider = new TranslatorProvider();
        $translatorProvider->register($container);


        $this->assertTrue($container->hasAlias('translator'));
        $this->assertTrue($container->has('Symfony\Component\Translation\TranslatorInterface'));
        $this->assertInstanceOf('Symfony\Component\Translation\TranslatorInterface', $container->get('translator'));
    }

    public function getContainer()
    {
        $container = new Container();
        return $container;
    }

    protected function getConfig()
    {
        $config = $this->getMockBuilder('Tomahawk\Config\ConfigInterface')->getMock();
        return $config;
    }
}
