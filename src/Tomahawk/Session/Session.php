<?php

namespace Tomahawk\Session;

use Symfony\Component\HttpFoundation\Session\Session as BaseSession;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use Tomahawk\Session\InputOldBag;

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

    public function __construct(SessionStorageInterface $storage)
    {
        $this->storage = $storage;
        $this->session = new BaseSession($storage);

        $inputOldBag = new InputOldBag('_input_old');
        $inputOldBag->setName('tomahawk_input_old');
        $this->session->registerBag($inputOldBag);

        // PHP Sessions auto start in 5.4
        if (version_compare(phpversion(), '5.4.0', '>=') && \PHP_SESSION_ACTIVE !== session_status()) {
            $this->session->start();
        }
    }

    /**
     * @return SessionStorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @return \Tomahawk\Session\InputOldBag
     */
    public function getOldBag()
    {
        return $this->session->getBag('tomahawk_input_old');
    }

    public function setOld($name, $value)
    {
        $this->getOldBag()->set($name, $value);
    }

    public function set($name, $value)
    {
        $this->session->set($name, $value);
    }

    public function get($name, $default = null)
    {
        return $this->session->get($name, $default);
    }

    public function has($name)
    {
        return $this->session->has($name);
    }

    public function remove($name)
    {
        $this->session->remove($name);
    }

    public function hasFlash($name)
    {
        return $this->getFlashBag()->has($name);
    }

    public function getFlash($name)
    {
        return $this->getFlashBag()->get($name);
    }

    public function setFlash($name, $value)
    {
        $this->getFlashBag()->set($name, $value);
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
     * Save Session
     *
     * @return $this
     */
    public function save()
    {
        $this->session->save();
        return $this;
    }
}