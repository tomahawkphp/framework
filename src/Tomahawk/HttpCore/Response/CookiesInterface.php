<?php

namespace Tomahawk\HttpCore\Response;

interface CookiesInterface
{
    public function set($name, $value);

    public function has($name);

    public function get($name, $default = null);

    public function expire($name);

    public function getQueued();
}
