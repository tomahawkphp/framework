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

class OldInputBag implements OldInputBagInterface
{
    private $name = '__old';

    /**
     * Old input
     *
     * @var array
     */
    private $oldInput = array();

    /**
     * The storage key for flashes in the session.
     *
     * @var string
     */
    private $storageKey;

    /**
     * Constructor.
     *
     * @param string $storageKey The key used to store flashes in the session.
     */
    public function __construct($storageKey = '_th_old_input')
    {
        $this->storageKey = $storageKey;
    }

    /**
     * Registers an input value
     *
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->oldInput[$name] = $value;
    }

    /**
     * Get input value for a given field
     *
     * @param $name
     * @param null $default
     * @return array
     */
    public function peek($name, $default = null)
    {
        return $this->has($name) ? $this->oldInput[$name] : $default;
    }

    /**
     * Gets all input values
     *
     * @return array
     */
    public function peekAll()
    {
        return $this->oldInput;
    }

    /**
     * Gets and clears input from the stack.
     *
     * @param string $name
     * @param null $default Default value if $name does not exist.
     *
     * @return array
     */
    public function get($name, $default = null)
    {
        if ( ! $this->has($name)) {
            return $default;
        }

        $return = $this->oldInput[$name];

        unset($this->oldInput[$name]);

        return $return;
    }

    /**
     * Gets and clears input from the stack.
     *
     * @return array
     */
    public function all()
    {
        $return = $this->peekAll();
        $this->oldInput = array();

        return $return;
    }

    /**
     * Sets all input values.
     *
     * @param array $input
     */
    public function setAll(array $input)
    {
        $this->oldInput = $input;
    }

    /**
     * Has value for a given input name?
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->oldInput) && $this->oldInput[$name];
    }

    /**
     * Returns a list of all defined types.
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->oldInput);
    }

    /**
     * Gets this bag's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Initializes the Bag.
     *
     * @param array $oldInput
     */
    public function initialize(array &$oldInput)
    {
        $this->oldInput = &$oldInput;
    }

    /**
     * Gets the storage key for this bag.
     *
     * @return string
     */
    public function getStorageKey()
    {
        return $this->storageKey;
    }

    /**
     * Clears out data from bag.
     *
     * @return mixed Whatever data was contained.
     */
    public function clear()
    {
        return $this->all();
    }
}
