<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tomahawk\Authentication\Encoder;

/**
 * Class BasePasswordEncoder
 *
 * @package Tomahawk\Authenticationentication\Encoder
 */
abstract class BasePasswordEncoder implements PasswordEncoderInterface
{
    const MAX_PASSWORD_LENGTH = 255;

    /**
     * Check if password is too long for this Password Encoder
     *
     * @param $rawPassword
     * @return bool
     */
    public function isPasswordTooLong($rawPassword)
    {
        return strlen($rawPassword) > static::MAX_PASSWORD_LENGTH;
    }
}
