<?php

namespace Tomahawk\Bundle\SwiftmailerBundle\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Tomahawk\Bundle\SwiftmailerBundle\TransportBuilder;

class TransportBuilderTest extends TestCase
{

    public function testBuildGmail()
    {
        $config = array(
            'transport' => 'gmail',
            'username'  => 'foo',
            'password'  => 'password',
        );

        $this->assertInstanceOf('Swift_SmtpTransport', TransportBuilder::build($config));
    }

    public function testBuildSmtp()
    {
        $config = array(
            'transport' => 'smtp',
            'username'  => 'foo',
            'password'  => 'password',
            'host'      => 'host@example.com',
            'port'      => 20,
            'security'  => 'ssl',
        );

        $this->assertInstanceOf('Swift_SmtpTransport', TransportBuilder::build($config));
    }

    public function testBuildSendMailTransport()
    {
        $config = array(
            'transport' => 'sendmail',
        );

        $this->assertInstanceOf('Swift_SendmailTransport', TransportBuilder::build($config));
    }

    public function testBuildMailTransport()
    {
        $config = array(
            'transport' => 'mail',
        );

        $this->assertInstanceOf('Swift_MailTransport', TransportBuilder::build($config));
    }

    public function testBuildNullTransport()
    {
        $config = array(
            'transport' => 'null',
        );

        $this->assertInstanceOf('Swift_NullTransport', TransportBuilder::build($config));
    }
}
