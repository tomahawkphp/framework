<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Hashing;

class Hasher implements HasherInterface
{

    /**
     * Default crypt cost factor.
     *
     * @var bool
     */
    protected $rounds = 8;

    /**
     * Hash the given value.
     *
     * @param  string $value
     * @param  array $options
     * @throws \RuntimeException
     * @return string
     */
    public function make($value, array $options = array())
    {
        $cost = isset($options['rounds']) ? $options['rounds'] : $this->rounds;

        $hash = $this->doHash($value, PASSWORD_BCRYPT, array('cost' => $cost));

        if ($hash === false) {
            throw new \RuntimeException("Bcrypt hashing not supported.");
        }

        return $hash;
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param  string  $value
     * @param  string  $hashedValue
     * @param  array   $options
     * @return bool
     */
    public function check($value, $hashedValue, array $options = array())
    {
        return password_verify($value, $hashedValue);
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string  $hashedValue
     * @param  array   $options
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = array())
    {
        $cost = isset($options['rounds']) ? $options['rounds'] : $this->rounds;
        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, array('cost' => $cost));
    }

    protected function doHash($value, $algo, array $options = array())
    {
        return password_hash($value, $algo, $options);
    }

}
