<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Session\Bag;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

interface OldInputBagInterface extends SessionBagInterface
{
    /**
     * Registers an input value
     *
     * @param $name
     * @param $value
     */
    public function set($name, $value);

    /**
     * Get input value for a given field
     *
     * @param $name
     * @param null $default
     * @return array
     */
    public function peek($name, $default = null);

    /**
     * Gets all input values
     *
     * @return array
     */
    public function peekAll();

    /**
     * Gets and clears input from the stack.
     *
     * @param string $name
     * @param null  $default Default value if $name does not exist.
     *
     * @return array
     */
    public function get($name, $default = null);

    /**
     * Gets and clears input from the stack.
     *
     * @return array
     */
    public function all();

    /**
     * Sets all input values.
     *
     * @param array $input
     */
    public function setAll(array $input);

    /**
     * Has value for a given input name?
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name);

    /**
     * Returns a list of all defined types.
     *
     * @return array
     */
    public function keys();
}
