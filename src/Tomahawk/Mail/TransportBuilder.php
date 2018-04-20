<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Mail;

/**
 * Class TransportBuilder
 * @package Tomahawk\Mail
 */
class TransportBuilder
{
    public static function build(array $config)
    {
        $type = $config['transport'];

        if ('gmail' === $type) {
            $transport = static::buildGmailTransport($config);
        }
        else if ('smtp' === $type) {
            $transport = static::buildSmtpTransport($config);
        }
        else if ('sendmail' === $type) {
            $transport = static::buildSendMailTransport();
        }
        else {
            $transport = static::buildNullTransport();
        }

        return $transport;
    }

    public static function buildGmailTransport(array $config)
    {
        $config['host'] = 'smtp.gmail.com';
        $config['port'] = 573;
        $config['security'] = 'ssl';

        return static::buildSmtpTransport($config);
    }

    public static function buildSmtpTransport(array $config)
    {
        $transport = new \Swift_SmtpTransport($config['host'], $config['port'], $config['security']);

        if (isset($config['username'])) {
            $transport->setUsername($config['username']);
            $transport->setPassword($config['password']);
        }

        return $transport;
    }

    public static function buildSendMailTransport()
    {
        return new \Swift_SendmailTransport();
    }

    public static function buildNullTransport()
    {
        return new \Swift_NullTransport();
    }

}
