<?php

namespace Tomahawk\Mail\Tests;

use PHPUnit\Framework\TestCase;
use Swift_NullTransport;
use Swift_SendmailTransport;
use Swift_SmtpTransport;
use Tomahawk\Mail\TransportBuilder;

class TransportBuilderTest extends TestCase
{
    public function testBuildGmail()
    {
        $config = array(
            'transport' => 'gmail',
            'username'  => 'foo',
            'password'  => 'password',
        );

        $this->assertInstanceOf(Swift_SmtpTransport::class, TransportBuilder::build($config));
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

        $this->assertInstanceOf(Swift_SmtpTransport::class, TransportBuilder::build($config));
    }

    public function testBuildSendMailTransport()
    {
        $config = array(
            'transport' => 'sendmail',
        );

        $this->assertInstanceOf(Swift_SendmailTransport::class, TransportBuilder::build($config));
    }

    public function testBuildNullTransport()
    {
        $config = array(
            'transport' => 'null',
        );

        $this->assertInstanceOf(Swift_NullTransport::class, TransportBuilder::build($config));
    }
}
