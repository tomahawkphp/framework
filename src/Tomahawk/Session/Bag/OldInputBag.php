<?php

namespace Tomahawk\Session\Bag;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

class OldInputBag implements SessionBagInterface
{

    /**
     * Gets this bag's name.
     *
     * @return string
     */
    public function getName()
    {
        // TODO: Implement getName() method.
    }

    /**
     * Initializes the Bag.
     *
     * @param array $array
     */
    public function initialize(array &$array)
    {
        // TODO: Implement initialize() method.
    }

    /**
     * Gets the storage key for this bag.
     *
     * @return string
     */
    public function getStorageKey()
    {
        // TODO: Implement getStorageKey() method.
    }

    /**
     * Clears out data from bag.
     *
     * @return mixed Whatever data was contained.
     */
    public function clear()
    {
        // TODO: Implement clear() method.
    }
}
