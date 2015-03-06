<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Session;

use Symfony\Component\HttpFoundation\Session\Session as BaseSession;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

class Session implements SessionInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $session;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface
     */
    protected $storage;

    /**
     * @var bool
     */
    protected $dirty = false;

    /**
     * @var string
     */
    protected $oldAttributeName = 'old_input';

    /**
     * @var string
     */
    protected $newAttributeName = 'new_input';

    public function __construct(SessionStorageInterface $storage)
    {
        $this->storage = $storage;
        $this->session = new BaseSession($storage);

        $oldInput = new AttributeBag('_input_old');
        $oldInput->setName($this->oldAttributeName);

        $newInput = new AttributeBag('_input_new');
        $newInput->setName($this->newAttributeName);

        $this->session->registerBag($oldInput);
        $this->session->registerBag($newInput);

        // PHP Sessions auto start in 5.4
        if (version_compare(phpversion(), '5.4.0', '>=') && \PHP_SESSION_ACTIVE !== session_status()) {
            $this->session->start();
        }
    }

    /**
     * Get session storage
     *
     * @return \Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Get old input bag
     *
     * @return \Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface
     */
    public function getOldInputBag()
    {
        return $this->session->getBag($this->oldAttributeName);
    }

    /**
     * Put value in old input bag
     *
     * @param $name
     * @param $value
     */
    public function setOldInput($name, $value)
    {
        $this->getOldInputBag()->set($name, $value);
    }

    /**
     * Get value from old input bag
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function getOldInput($name, $default = null)
    {
        return $this->getOldInputBag()->get($name, $default);
    }

    /**
     * Check if a value is in the old input bag
     *
     * @param $name
     * @return bool
     */
    public function hasOldInput($name)
    {
        return $this->getOldInputBag()->has($name);
    }

    /**
     * Get default session bag
     *
     * @return \Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface
     */
    public function getNewInputBag()
    {
        return $this->session->getBag($this->newAttributeName);
    }

    /**
     * Put a value in the new input bag
     *
     * @param $name
     * @param $value
     */
    public function setNewInput($name, $value)
    {
        $this->getNewInputBag()->set($name, $value);
    }

    /**
     * Get a value from the new input bag
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function getNewInput($name, $default = null)
    {
        return $this->getNewInputBag()->get($name, $default);
    }

    /**
     * Check if a value is in the new input bag
     *
     * @param $name
     * @return bool
     */
    public function hasNewInput($name)
    {
        return $this->getNewInputBag()->has($name);
    }

    /**
     * Set a value in the session
     *
     * @param $name
     * @param $value
     * @return $this
     */
    public function set($name, $value)
    {
        // If value is different, then the session is dirty
        if ($this->session->get($name) !== $value) {
            $this->dirty = true;
        }
        $this->session->set($name, $value);

        return $this;
    }

    /**
     * Get a value off the session
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return $this->session->get($name, $default);
    }

    /**
     * Check if a session value exists
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return $this->session->has($name);
    }

    /**
     * Remove a value from the session
     *
     * @param $name
     * @return $this
     */
    public function remove($name)
    {
        $this->session->remove($name);
        $this->dirty = true;
        return $this;
    }

    /**
     * Check if flash value exists
     *
     * @param $name
     * @return bool
     */
    public function hasFlash($name)
    {
        return $this->getFlashBag()->has($name);
    }

    /**
     * Get flash values
     *
     * @param $name
     * @param array $default
     * @return array
     */
    public function getFlash($name, $default = array())
    {
        return $this->getFlashBag()->get($name, $default);
    }

    /**
     * Flash a value
     *
     * @param $name
     * @param $value
     * @return $this
     */
    public function setFlash($name, $value)
    {
        $this->getFlashBag()->set($name, $value);
        $this->dirty = true;
        return $this;
    }

    /**
     * Get Flash Bag
     *
     * @return \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface
     */
    public function getFlashBag()
    {
        return $this->session->getFlashBag();
    }

    /**
     * Check if session is dirty
     *
     * @return bool
     */
    public function isDirty()
    {
        return true === $this->dirty;
    }

    /**
     * Get input from both new and old input bags
     *
     * @return array
     */
    public function getInputData()
    {
        return array_merge($this->getOldInputBag()->all(), $this->getNewInputBag()->all());
    }

    /**
     * Reflash old input to use on next request
     *
     * @return $this
     */
    public function reflashInput()
    {
        $oldInput = $this->getOldInputBag()->all();
        $this->getNewInputBag()->replace($oldInput);
        $this->dirty = true;
        return $this;
    }

    /**
     * Clear new input
     *
     * @return $this
     */
    public function clearNewInput()
    {
        $hasInput = count($this->getNewInputBag()->all()) > 0;
        $this->getNewInputBag()->clear();

        if ($hasInput) {
            $this->dirty = true;
        }
        return $this;
    }

    /**
     * Clear old input
     *
     * @return $this
     */
    public function clearOldInput()
    {
        $hasInput = count($this->getOldInputBag()->all()) > 0;
        $this->getOldInputBag()->clear();

        if ($hasInput) {
            $this->dirty = true;
        }
        return $this;
    }

    /**
     * Merge new input into old input bag
     *
     * @return $this
     */
    public function mergeNewInput()
    {
        $this->getOldInputBag()->replace($this->getNewInputBag()->all());
        $this->dirty = true;
        return $this;
    }

    /**
     * Save Session
     *
     * @return $this
     */
    public function save()
    {
        // We only update the session if the session data has changed
        if ($this->isDirty()) {
            $this->session->save();
            $this->dirty = false;
        }
        return $this;
    }
}
