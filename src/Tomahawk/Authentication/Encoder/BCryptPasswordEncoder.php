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

use Tomahawk\Authentication\Exception\BadCredentialsException;

/**
 * Class BcryptPasswordEncoder
 *
 * Inspired by Symfony's BcryptPasswordEncoder
 *
 * @package Tomahawk\Authenticationentication\Encoder
 */
class BCryptPasswordEncoder extends BasePasswordEncoder
{
    const MAX_PASSWORD_LENGTH = 72;

    /**
     * @var int
     */
    private $cost;

    /**
     * BCryptPasswordEncoder constructor.
     *
     * @param int $cost
     */
    public function __construct($cost = 10)
    {
        $cost = (int)$cost;
        $this->cost = $cost;
    }

    /**
     * Encode Password
     *
     * @param $rawPassword
     * @param $salt
     * @return string
     */
    public function encodePassword($rawPassword, $salt)
    {
        if ($this->isPasswordTooLong($rawPassword)) {
            throw new BadCredentialsException();
        }

        $options = array(
            'cost' => $this->cost
        );

        return password_hash($rawPassword, PASSWORD_BCRYPT, $options);
    }

    /**
     * Check if password is valid
     *
     * @param $encodedPassword
     * @param $rawPassword
     * @param $salt
     * @return bool
     */
    public function isPasswordValid($encodedPassword, $rawPassword, $salt)
    {
        return ! $this->isPasswordTooLong($rawPassword) && password_verify($rawPassword, $encodedPassword);
    }
}
