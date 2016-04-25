<?php

namespace Tomahawk\Session\Bag;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

interface OldInputBagInterface extends SessionBagInterface
{
    /**
     * Adds a flash message for type.
     *
     * @param string $type
     * @param string $message
     */
    public function add($type, $message);

    /**
     * Registers a message for a given type.
     *
     * @param string       $type
     * @param string|array $message
     */
    public function set($type, $message);

    /**
     * Gets flash messages for a given type.
     *
     * @param string $type    Message category type.
     * @param array  $default Default value if $type does not exist.
     *
     * @return array
     */
    public function peek($type, array $default = array());

    /**
     * Gets all flash messages.
     *
     * @return array
     */
    public function peekAll();

    /**
     * Gets and clears flash from the stack.
     *
     * @param string $type
     * @param array  $default Default value if $type does not exist.
     *
     * @return array
     */
    public function get($type, array $default = array());

    /**
     * Gets and clears flashes from the stack.
     *
     * @return array
     */
    public function all();

    /**
     * Sets all flash messages.
     */
    public function setAll(array $messages);

    /**
     * Has flash messages for a given type?
     *
     * @param string $type
     *
     * @return bool
     */
    public function has($type);

    /**
     * Returns a list of all defined types.
     *
     * @return array
     */
    public function keys();
}
