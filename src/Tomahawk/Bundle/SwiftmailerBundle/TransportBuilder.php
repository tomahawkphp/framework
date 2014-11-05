<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\SwiftmailerBundle;

class TransportBuilder
{
    public static function build(array $config)
    {
        $type = $config['transport'];

        switch ($type)
        {
            case 'gmail':
                $transport = self::buildGmailTransport($config);
                break;
            case 'smtp':
                $transport = self::buildSmtpTransport($config);
                break;
            case 'sendmail':
                $transport = self::buildSendMailTransport();
                break;
            case 'mail':
                $transport = self::buildMailTransport();
                break;
            case 'null':
            default:
                $transport = self::buildNullTransport();
                break;
        }

        return $transport;
    }

    public static function buildGmailTransport(array $config)
    {
        $config['host'] = 'smtp.gmail.com';
        $config['port'] = 573;
        $config['security'] = 'ssl';

        return self::buildSmtpTransport($config);
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

    public static function buildMailTransport()
    {
        return new \Swift_MailTransport();
    }

    public static function buildNullTransport()
    {
        return new \Swift_NullTransport();
    }

}
