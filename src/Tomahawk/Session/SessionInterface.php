<?php

namespace Tomahawk\Session;

interface SessionInterface
{
    public function getOldBag();

    public function setOld($name, $value);

    public function set($name, $value);

    public function get($name, $default = null);

    public function getFlash($name);

    public function setFlash($name, $value);

    public function getFlashBag();

    public function save();
}