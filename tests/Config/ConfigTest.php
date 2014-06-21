<?php

use Tomahawk\Config\ConfigManager;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testSingleConfig()
    {
        $dirs = array(__DIR__ .'/configs');
        $config = new ConfigManager($dirs);

        $config->load();

        $this->assertEquals('pdo', $config->get('auth.driver'));
        $this->assertEquals('database', $config->get('session.driver'));
    }

    public function testEnvConfig()
    {
        $dirs = array(
            __DIR__ .'/configs',
            __DIR__ .'/configs/develop'
        );

        $config = new ConfigManager($dirs);

        $config->load();

        $this->assertEquals('eloquent', $config->get('auth.driver'));
        $this->assertEquals('cookie', $config->get('session.driver'));
    }


}