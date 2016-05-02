<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\Bundle\FrameworkBundle\DependencyInjection\TranslatorServiceProvider as TranslatorProvider;

class TranslatorServiceProviderTest extends TestCase
{
    /**
     * @covers \Tomahawk\Bundle\FrameworkBundle\DependencyInjection\TranslatorServiceProvider
     */
    public function testProvider()
    {
        $tranlationsDir = array(
            __DIR__ . '/Resources/translations',
        );

        $config = $this->getConfig();
        $config
            ->method('get')
            ->will($this->returnValueMap(array(
                array('translation.locale', null, 'en'),
                array('translation.fallback_locale', null, 'en'),
                array('translation.translation_dirs', null, $tranlationsDir),
                array('translation.cache_dir', null, null),
            )));

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
        $config = $this->getMock('Tomahawk\Config\ConfigInterface');
        return $config;
    }
}
