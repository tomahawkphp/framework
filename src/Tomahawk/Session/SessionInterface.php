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

interface SessionInterface
{
    public function has($name);

    public function remove($name);

    public function getOldBag();

    public function setOld($name, $value);

    public function set($name, $value);

    public function get($name, $default = null);

    public function getFlash($name);

    public function setFlash($name, $value);

    public function getFlashBag();

    public function save();
}
