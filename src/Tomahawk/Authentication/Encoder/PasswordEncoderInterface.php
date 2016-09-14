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

interface PasswordEncoderInterface
{
    /**
     * Encode Password
     *
     * @param $rawPassword
     * @param $salt
     * @return string
     */
    public function encodePassword($rawPassword, $salt);

    /**
     * Check if password is valid
     *
     * @param $encodedPassword
     * @param $rawPassword
     * @param $salt
     * @return bool
     */
    public function isPasswordValid($encodedPassword, $rawPassword, $salt);

    /**
     * Check if password is too long
     *
     * @param $rawPassword
     * @return bool
     */
    public function isPasswordTooLong($rawPassword);
}
